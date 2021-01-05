<?php
declare(strict_types=1);

namespace FmLabs\Q\Adapter;

use FmLabs\Q\QueueMessageInterface;

class RedisAdapter extends BaseAdapter implements \Countable
{
    /**
     * @var array Default configuration
     */
    protected $defaultConfig = [
        'host' => 'localhost',
        'port' => 6379,
        'user' => 'guest',
        'pass' => 'guest',
        'queue_name' => null,
        // change the following options only if you know what you are doing
    ];

    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @var string
     */
    private $queueName;

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
        $redis = new \Redis();
        if (!$redis->connect($this->getConfig('host'), $this->getConfig('port'))) {
            throw new \Exception('Redis client connection failed');
        }

        $this->redis = $redis;
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function disconnect(): void
    {
        $this->log(' [redis] Disconnect');
        if ($this->redis) {
            $this->redis->close();
        }
    }

    /**
     * @inheritDoc
     */
    public function push(QueueMessageInterface $msg): void
    {
        # QueueMessage -> Redis
        if ($this->redis->rPush($this->queueName, $msg->serialize()) === false) {
            throw new \RuntimeException('[redis] Failed to push to queue');
        }
        $this->log(' [redis] Published message: ' . $msg);
    }

    /**
     * @inheritDoc
     */
    public function pop(): ?QueueMessageInterface
    {
        $this->log(' [redis] Pull message');

        $next = $this->redis->lPop($this->queueName);
        if (!$next) {
            $this->log(' [redis] No messages');

            return null;
        }

        # Redis -> QueueMessage
        $this->log(' [redis] Received message: ' . $next);
        $msg = $this->restoreMessage($next);

        # Acknowledge message
        # (not supported yet)

        $this->log(' [redis] Returning message: ' . $msg);

        return $msg;
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return $this->redis->lLen($this->queueName);
    }

    /**
     * Access to low-level AMQP connection.
     *
     * @return \Redis|null
     */
    public function getConnection(): ?\Redis
    {
        return $this->redis;
    }
}
