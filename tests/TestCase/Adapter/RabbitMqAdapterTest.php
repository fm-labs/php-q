<?php
declare(strict_types=1);

namespace FmLabs\Q\Test\TestCase\Adapter;

use FmLabs\Q\Adapter\RabbitMqAdapter;
use FmLabs\Q\QueueAdapterInterface;

/**
 * Class RabbitMqAdapterTest
 *
 * @package FmLabs\Q\Test\TestCase\Adapter
 * @group adapter
 * @group rabbitmq
 * @group amqp
 */
class RabbitMqAdapterTest extends SharedAdapterTest
{
    /**
     * @var string
     */
    protected static $queueName = 'test.phpq';

    /**
     * @var \FmLabs\Q\Adapter\RabbitMqAdapter
     */
    protected static $adapter;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$queueName = 'test.phpq.' . time();
    }

    /**
     * @return \FmLabs\Q\QueueAdapterInterface
     */
    public function getAdapter(): QueueAdapterInterface
    {
        if (!static::$adapter) {
            static::$adapter = new RabbitMqAdapter([
                'host' => getenv('TEST_RABBITMQ_HOST') ?: 'localhost',
                'port' => getenv('TEST_RABBITMQ_PORT') ?: 5672,
                'user' => getenv('TEST_RABBITMQ_USER') ?: 'guest',
                'pass' => getenv('TEST_RABBITMQ_PASS') ?: 'guest',
                'queue_name' => static::$queueName,
                'queue_ttl' => 60000,
                'msg_ttl' => 60000,
                'auto_delete' => true,
                'durable' => false,
            ]);
        }

        return static::$adapter;
    }

    /**
     * @return void
     */
    protected function wait(): void
    {
        usleep(100000);
    }

    /**
     * Little hack to make sure the queue will be deleted after all tests have been executed.
     * Issue: The AMQP queue will not be auto-deleted as long as not a single consumer subscription gets canceled.
     * Workaround: Subscribe to channel and immediately unsubscribe.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        if (static::$adapter) {
            $consumerTag = 'test.consumer.' . time();
            $channel = static::$adapter->getChannel();
            $channel->basic_consume(static::$queueName, $consumerTag, false, false, true, true);
            $channel->basic_cancel($consumerTag, true, true);
        }
    }
}
