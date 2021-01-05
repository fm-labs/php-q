<?php
declare(strict_types=1);

namespace FmLabs\Q;

/**
 * Interface QueueInterface
 *
 * @package FmLabs\Q
 */
interface QueueInterface
{
    /**
     * Publish message to queue.
     *
     * @param \FmLabs\Q\QueueMessageInterface $msg Queue message
     * @return void
     */
    public function push(QueueMessageInterface $msg);

    /**
     * Consume next message in queue, if queue is not empty.
     * Returns NULL if queue is empty.
     *
     * @return null|\FmLabs\Q\QueueMessageInterface
     */
    public function pop(): ?QueueMessageInterface;

    /**
     * @return void
     */
    //public function popAtomic(?callable $callback = null): void;
}
