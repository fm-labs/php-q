<?php
declare(strict_types=1);

namespace FmLabs\Q\Message;

use FmLabs\Q\QueueMessageInterface;

abstract class BaseMessage implements QueueMessageInterface
{
    /**
     * Create message from arbitrary data.
     * Throws \InvalidArgument exception, when invalid data has been passed.
     *
     * @param mixed $data Data to create message from
     * @return \FmLabs\Q\QueueMessageInterface
     * @throws \InvalidArgumentException
     */
    public static function create($data): QueueMessageInterface
    {
        $instance = new static();
        $instance->setPayload($data);

        return $instance;
    }

    /**
     * Restore message from serialized state.
     * Throws \InvalidArgument exception, when invalid data has been passed.
     *
     * @param mixed $data Serialized data to create message from
     * @return \FmLabs\Q\QueueMessageInterface
     * @throws \InvalidArgumentException
     */
    public static function restore($data): QueueMessageInterface
    {
        $instance = new static();
        $instance->unserialize($data);

        return $instance;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->data;
    }

    /**
     * Serialize Message to string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->serialize();
    }
}
