<?php
declare(strict_types=1);

namespace FmLabs\Q\Message;

class DataMessage extends BaseMessage
{
    /**
     * @var array Payload data
     */
    protected $data;

    /**
     * DataMessage constructor.
     *
     * @param array $data Payload data
     */
    public function __construct(array $data = [])
    {
        $this->setPayload($data);
    }

    /**
     * @param mixed $data Message payload
     * @return void
     */
    public function setPayload($data)
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Only array payload allowed');
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
        //@TODO Check for json encoding errors (throw on error)
        return json_encode($this->data);
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
        //@TODO Check for json decoding errors (throw on error)
        $this->data = json_decode($serialized, true);
    }
}
