<?php
declare(strict_types=1);

namespace FmLabs\Q\Message;

/**
 * Class TextMessage
 *
 * @package FmLabs\Q\Message
 */
class TextMessage extends BaseMessage
{
    /**
     * @var string Payload string
     */
    protected $data;

    /**
     * DataMessage constructor.
     *
     * @param string $data Payload data
     */
    public function __construct(?string $data = null)
    {
        $this->setPayload($data);
    }

    /**
     * @param mixed $data Message payload
     * @return void
     */
    public function setPayload($data)
    {
        if ($data !== null && !is_string($data)) {
            throw new \InvalidArgumentException('Only text payload allowed');
        }
        $this->data = $data;
    }

    /**
     * String representation of object.
     *
     * @link https://php.net/manual/en/serializable.serialize.php
     * @return string|null The string representation of the object or null
     * @throws \Exception Returning other type than string or null
     */
    public function serialize()
    {
        return $this->data;
    }

    /**
     * Constructs the object.
     *
     * @link https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized The string representation of the object.
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->data = $serialized;
    }
}
