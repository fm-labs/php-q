<?php
declare(strict_types=1);

namespace FmLabs\Q;

use FmLabs\Q\Adapter\MemoryAdapter;
use FmLabs\Q\Message\TextMessage;

class Q
{
    protected static $configured = [
        'default' => [
            'queueClass' => MemoryAdapter::class,
            'adapterClass' => Adapter\MemoryAdapter::class,
        ],
    ];

    protected static $queues = [];

    /**
     * @param string $queueName Queue name
     * @param array|null $config Queue configuration
     * @return string[]|null|void
     */
    public static function config(string $queueName, ?array $config = null)
    {
        if ($config === null) {
            return static::$configured[$queueName] ?? null;
        }

        if (static::$configured[$queueName] ?? false) {
            throw new \RuntimeException("Queue $queueName already configured");
        }

        static::$configured[$queueName] = $config;
    }

    /**
     * @param string $queueName Queue name
     * @return \FmLabs\Q\QueueInterface
     */
    public static function get(string $queueName): QueueInterface
    {
        if (!isset(static::$queues[$queueName])) {
            $config = self::$configured[$queueName] ?? null;
            if (!$config) {
                throw new \RuntimeException("Queue not configured: $queueName");
            }
            $queueClass = $config['queueClass'];
            unset($config['queueClass']);
            $adapterClass = $config['adapterClass'];
            unset($config['adapterClass']);
            $msgClass = $config['messageClass'] ?? TextMessage::class;
            unset($config['messageClass']);
            $adapter = static::makeAdapter($adapterClass, [$config]);
            $queue = static::makeQueue($queueClass, [$adapter, $msgClass]);
            static::$queues[$queueName] = $queue;
        }

        return static::$queues[$queueName];
    }

    /**
     * @param string $className Queue class
     * @param array $constructorArgs Constructor args
     * @return \FmLabs\Q\QueueInterface
     */
    public static function makeQueue(string $className, array $constructorArgs = []): QueueInterface
    {
        if (!class_exists($className)) {
            throw new \RuntimeException("Class not found: $className");
        }

        $instance = new $className(...$constructorArgs);
        if (!($instance instanceof QueueInterface)) {
            throw new \RuntimeException('Class does not implement QueueInterface');
        }

        return $instance;
    }

    /**
     * @param string $className Queue class
     * @param array $constructorArgs Constructor args
     * @return \FmLabs\Q\QueueAdapterInterface
     */
    public static function makeAdapter(string $className, array $constructorArgs = []): QueueAdapterInterface
    {
        if (!class_exists($className)) {
            throw new \RuntimeException("Class not found: $className");
        }

        $instance = new $className(...$constructorArgs);
        if (!($instance instanceof QueueAdapterInterface)) {
            throw new \RuntimeException('Class does not implement QueueAdapterInterface');
        }

        return $instance;
    }

    /**
     * @param string $queueName Queue name
     * @param \FmLabs\Q\QueueMessageInterface $msg Queue message
     * @return void
     */
    public static function push(string $queueName, QueueMessageInterface $msg): void
    {
        static::get($queueName)->push($msg);
    }

    /**
     * @param string $queueName Queue name
     * @return \FmLabs\Q\QueueMessageInterface
     */
    public static function pop(string $queueName): ?QueueMessageInterface
    {
        return static::get($queueName)->pop();
    }

    /**
     * @param string $queueName Queue name
     * @param \FmLabs\Q\QueueMessageInterface $msg Queue message
     * @return void
     */
    public static function reject(string $queueName, QueueMessageInterface $msg): void
    {
        //@TODO Implement me
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param string $queueName Queue name
     * @param \FmLabs\Q\QueueMessageInterface $msg Queue message
     * @return void
     */
    public static function requeue(string $queueName, QueueMessageInterface $msg): void
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @param string $queueName Queue name
     * @param \FmLabs\Q\QueueMessageInterface $msg Queue message
     * @return void
     */
    public static function drop(string $queueName, QueueMessageInterface $msg): void
    {
        throw new \RuntimeException('Not implemented');
    }
}
