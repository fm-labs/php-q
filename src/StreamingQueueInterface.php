<?php
declare(strict_types=1);

namespace FmLabs\Q;

/**
 * Interface StreamingQueueInterface
 *
 * @package FmLabs\Q
 */
interface StreamingQueueInterface
{
    /**
     * Subscribe to queue
     *
     * @param callable|null $callback Callback invoked every time a message is received from the queue.
     *    The callback will be called with a QueueMessageInterface as first arg and only argument.
     * @return void
     */
    public function subscribe(?callable $callback);
}
