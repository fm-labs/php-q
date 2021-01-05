<?php
declare(strict_types=1);

namespace FmLabs\Q;

interface QueueMessageInterface extends \Serializable
{
    /**
     * @param mixed $data Message payload
     * @return void
     * @throws \InvalidArgumentException
     */
    public function setPayload($data);

    /**
     * @return mixed
     */
    public function getPayload();

    /**
     * Serialize Message to string.
     *
     * @return string
     */
    public function __toString(): string;
}
