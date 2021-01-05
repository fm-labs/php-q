<?php
declare(strict_types=1);

namespace FmLabs\Q\Test\TestCase\Adapter;

use FmLabs\Q\Adapter\RedisAdapter;
use FmLabs\Q\QueueAdapterInterface;

/**
 * Class RedisAdapterTest
 *
 * @package FmLabs\Q\Test\TestCase\Adapter
 * @group adapter
 * @group redis
 */
class RedisAdapterTest extends SharedAdapterTest
{
    /**
     * @var string
     */
    protected static $queueName = 'test_phpq';

    /**
     * @var \FmLabs\Q\Adapter\RedisAdapter
     */
    protected static $adapter;

    /**
     * @inheritDoc
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        static::$queueName = 'test_phpq' . time();
    }

    /**
     * @return \FmLabs\Q\QueueAdapterInterface
     */
    public function getAdapter(): QueueAdapterInterface
    {
        if (!static::$adapter) {
            static::$adapter = new RedisAdapter([
                'host' => getenv('TEST_REDIS_HOST') ?: 'localhost',
                'port' => getenv('TEST_REDIS_PORT') ?: 6379,
                'user' => getenv('TEST_REDIS_USER') ?: null,
                'pass' => getenv('TEST_REDIS_PASS') ?: null,
                'queue_name' => static::$queueName,
            ]);
        }

        return static::$adapter;
    }

    /**
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
    }
}
