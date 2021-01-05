<?php
declare(strict_types=1);

namespace FmLabs\Q\Queue;

use FmLabs\Q\QueueAdapterInterface;
use FmLabs\Q\QueueMessageInterface;

/**
 * Class BlockingQueue
 *
 * @package FmLabs\Q\Queue
 */
class BlockingQueue implements \FmLabs\Q\QueueInterface
{
    /**
     * @var \FmLabs\Q\QueueAdapterInterface
     */
    private $adapter;

    /**
     * BaseQueue constructor.
     *
     * @param \FmLabs\Q\QueueAdapterInterface $adapter Queue adapter instance
     */
    public function __construct(QueueAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Direct access to low-level queue adapter.
     *
     * @return \FmLabs\Q\QueueAdapterInterface
     */
    public function getAdapter(): QueueAdapterInterface
    {
        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function push(QueueMessageInterface $msg)
    {
        $this->adapter->push($msg);
    }

    /**
     * @inheritDoc
     */
    public function pop(): ?QueueMessageInterface
    {
        $start = time();
        $timeout = 10;
        $next = null;
        while ($next === null && ($timeout === 0 || $start + $timeout < time())) {
            $next = $this->adapter->pop();
            sleep(1);
        }

        return $next;
    }
}
