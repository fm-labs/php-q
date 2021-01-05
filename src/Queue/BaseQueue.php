<?php
declare(strict_types=1);

namespace FmLabs\Q\Queue;

use FmLabs\Q\QueueAdapterInterface;
use FmLabs\Q\QueueMessageInterface;

/**
 * Class BaseQueue
 *
 * The simplest implementation of QueueInterface using a QueueAdapterInterface.
 *
 * @package FmLabs\Q\Queue
 */
class BaseQueue implements \FmLabs\Q\QueueInterface
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
        return $this->adapter->pop();
    }
}
