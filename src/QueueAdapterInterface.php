<?php
declare(strict_types=1);

namespace FmLabs\Q;

use FmLabs\Q\Message\TextMessage;

/**
 * Interface QueueAdapterInterface
 *
 * @package FmLabs\Q
 */
interface QueueAdapterInterface extends QueueInterface
{
    /**
     * Sets the queue message class used to serialize/unserialize queue data.
     *
     * @param string $className Message class name
     * @return $this
     */
    public function setMessageClass(string $className = TextMessage::class);

}
