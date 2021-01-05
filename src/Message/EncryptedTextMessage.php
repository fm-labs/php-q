<?php
declare(strict_types=1);

namespace FmLabs\Q\Message;

class EncryptedTextMessage extends BaseMessage
{
    /**
     * @var string Plaintext message string
     */
    private $data;

    /**
     * @var string Encrypted message string
     */
    private $encrypted;

    /**
     * @var string De-/Encryption key
     */
    private $secret = 's3cret';

    /**
     * DataMessage constructor.
     *
     * @param string|null $plainText Payload data
     */
    public function __construct(?string $plainText = null)
    {
        $this->setPayload($plainText);
    }

    /**
     * @param mixed $data Message payload
     * @return void
     */
    public function setPayload($data)
    {
        if (!is_string($data)) {
            throw new \InvalidArgumentException('Only text payload allowed');
        }
        $this->data = $data;
        $this->encrypted = null;
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
        return $this->getEncrypted();
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
        $this->data = $this->decrypt($serialized, $this->secret);
    }

    private function getEncrypted(): string
    {
        if (!$this->encrypted) {
            if (!$this->data) {
                throw new \RuntimeException('Can not encrypt empty message');
                //return '';
            }
            $this->encrypted = $this->encrypt($this->data, $this->secret);
        }

        return $this->encrypted;
    }

    private function encrypt(string $plain, string $secret): string
    {
        $encrypted = $plain;
        //@TODO Encrypt string
        return $encrypted;
    }

    private function decrypt(string $encrypted, string $secret): string
    {
        $plain = $encrypted;
        //@TODO Decrypt string
        return $plain;
    }
}
