<?php
declare(strict_types=1);

namespace FmLabs\Q\Adapter;

use FmLabs\Q\QueueMessageInterface;
use FmLabs\Q\StreamingQueueInterface;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMqAdapter extends BaseAdapter implements \Countable, StreamingQueueInterface
{
    protected const DELIVERY_PERSISTENT = 1;
    protected const DELIVER_TRANSIENT = 2;

    /**
     * @var array Default configuration
     */
    protected $defaultConfig = [
        'host' => 'localhost',
        'port' => 5672,
        'user' => 'guest',
        'pass' => 'guest',
        'queue_name' => null,
        // change the following options only if you know what you are doing
        'passive' => false,
        'durable' => true,
        'exclusive' => false,
        'auto_delete' => false,
        'queue_ttl' => -1,
        'msg_ttl' => -1,
    ];

    /**
     * @var string
     */
    private $queueName;

    /**
     * @var \PhpAmqpLib\Connection\AMQPStreamConnection
     */
    private $connection;

    /**
     * @var \PhpAmqpLib\Channel\AMQPChannel
     */
    private $channel;

    /**
     * RabbitMq constructor.
     *
     * @param array $config Adapter config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->queueName = $this->getConfig('queue_name');
        if (!$this->queueName) {
            throw new \InvalidArgumentException('Missing parameter: queue_name');
        }

        $this->connect();
    }

    /**
     * @throws \Exception
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * @return void
     */
    protected function connect()
    {
        $cnf =& $this->config;
        # create rabbitmq connection
        $this->log(sprintf(
            ' [rmq] Connecting %s:%s@%s:%s',
            $cnf['host'],
            $cnf['port'],
            $cnf['user'],
            $cnf['pass']
        ));
        $this->connection = new AMQPStreamConnection($cnf['host'], $cnf['port'], $cnf['user'], $cnf['pass']);

        $queueArgs = new AMQPTable();
        # message expiration
        if ($this->getConfig('msg_ttl', 0) > 0) {
            $queueArgs->set('x-message-ttl', $this->getConfig('msg_ttl'));
        }

        # request channel and queue
        $channel = $this->connection->channel();
        $channel->queue_declare(
            $this->queueName, #queue - Queue names may be up to 255 bytes of UTF-8 characters
            $this->getConfig('passive', false), #passive - can use this to check whether an exchange exists without modifying the server state
            $this->getConfig('durable', false), #durable, make sure that RabbitMQ will never lose our queue if a crash occurs - the queue will survive a broker restart
            $this->getConfig('exclusive', false), #exclusive - used by only one connection and the queue will be deleted when that connection closes
            $this->getConfig('auto_delete', false), #auto delete - queue is deleted when last consumer unsubscribes
            false,
            $queueArgs
        );

        $this->channel = $channel;
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function disconnect(): void
    {
        $this->log(' [rmq] Disconnect');
        if ($this->channel) {
            $this->channel->close();
        }
        if ($this->connection) {
            $this->connection->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function push(QueueMessageInterface $msg): void
    {
        # QueueMessage -> AMQPMessage
        $props = [
            //'content_type' => 'text/plain', #Content type, e.g. "application/json". Used by applications, not core RabbitMQ
            //'content_encoding' => null, # Content encoding, e.g. "gzip". Used by applications, not core RabbitMQ
            //'application_headers' => [], # An arbitrary map of headers with string header names
            //'delivery_mode' => static::DELIVER_TRANSIENT, # 2 for "persistent", 1 for "transient".
            //'priority' => null,
            //'correlation_id' => null, # Helps correlate requests with responses
            //'reply_to' => null, # Carries response queue name
            //'expiration' => null, # Per-message TTL
            //'message_id' => null, # Arbitrary message ID
            //'timestamp' => time(), # Application-provided timestamp
            //'type' => null, # Application-specific message type, e.g. "orders.created"
            //'user_id' => null, # User ID, validated if set
            //'app_id' => null, # Application name
            //'cluster_id' => null,
        ];

        # @TODO Implement Per-message TTL in publishers
        //if ($this->getConfig('msg_ttl', 0) > 0) {
        //    $props['x-message-ttl'] = $this->getConfig('msg_ttl');
        //}
        $amqpMsg = new AMQPMessage(
            $msg->serialize(),
            $props
        );
        $this->channel->basic_publish($amqpMsg, '', $this->queueName);

        $this->log(' [rmq] Published message: ' . $msg);
    }

    /**
     * @inheritDoc
     */
    public function pop(): ?QueueMessageInterface
    {
        $this->log(' [rmq] Pull message');
        $msg = $this->channel->basic_get($this->queueName);

        if (!$msg) {
            $this->log(' [rmq] No messages');

            return null;
        }

        $qMsg = $this->handleMessage($msg);
        $this->log(' [rmq] Returning message: ' . $qMsg);

        return $qMsg;
    }

    /**
     * Subscribe to queue
     *
     * @param callable|null $callback Callback invoked every time a message is received from the queue.
     *    The callback will be called with a QueueMessageInterface as first arg and only argument.
     * @return void
     * @throws \ErrorException
     */
    public function subscribe(?callable $callback = null): void
    {
        echo ' [rmq] Waiting for messages. To exit press CTRL+C', "\n";

        $handle = function (AMQPMessage $msg) use ($callback) {
            $this->handleMessage($msg, $callback);
        };
        $this->channel->basic_consume($this->queueName, '', false, false, false, false, $handle);

        while ($this->channel->is_consuming()) {
            $this->channel->wait();
        }
    }

    /**
     * Handle a new message received from AMQP queue.
     * The message will be acknowledged after the user callback has been called successfully.
     * If the user callback throws an exception. The message will be put back on the queue.
     *
     * @param \PhpAmqpLib\Message\AMQPMessage $msg The received AMQP message
     * @param callable|null $callback Optional user callback
     * @return \FmLabs\Q\QueueMessageInterface The queue message constructed from AMQP message
     * @throws \Exception
     */
    private function handleMessage(AMQPMessage $msg, ?callable $callback = null): QueueMessageInterface
    {
        $this->log(' [rmq] Received message: ' . $msg->body);
        # AMQPMessage -> QueueMessage
        $qMsg = $this->restoreMessage($msg->body);

        try {
            if ($callback) {
                call_user_func($callback, $qMsg);
            }

            # Acknowledge message
            $this->log(' [rmq] Ack message: #' . $msg->getDeliveryTag());
            $msg->ack();

            return $qMsg;
        } catch (\Exception $ex) {
            $this->log(' [rmq] ERROR: ' . $ex->getMessage());
            $msg->nack(true);

            throw $ex;
        }
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        # Request message count from channel (using passive queue declaration)
        $queueInfo = $this->channel->queue_declare($this->queueName, true);
        if ($queueInfo) {
            [,$msgCount,] = $queueInfo;

            return $msgCount;
        }

        # Alternate: Request count via rabbitmq management http api
        /**
         * conn = kombu.Connection('amqp://userid:password@10.111.123.54:5672/vhost')
        conn.connect()
        client = conn.get_manager()
        queues = client.get_queues('vhost')
        for queue in queues:
        if queue == queue_name:
        print("tasks waiting in queue:"+str(queue.get("messages_ready")))
        print("tasks currently running:"+str(queue.get("messages_unacknowledged")))
         */
        return -1;
    }

    /**
     * Access to low-level AMQP connection.
     *
     * @return \PhpAmqpLib\Connection\AMQPStreamConnection|null
     */
    public function getConnection(): ?AMQPStreamConnection
    {
        return $this->connection;
    }

    /**
     * Access to low-level AMQP channel.
     *
     * @return \PhpAmqpLib\Channel\AMQPChannel|null
     */
    public function getChannel(): ?AMQPChannel
    {
        return $this->channel;
    }
}
