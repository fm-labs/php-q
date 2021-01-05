<?php
declare(strict_types=1);

namespace FmLabs\Q\Adapter;

use FmLabs\Q\Message\TextMessage;
use FmLabs\Q\QueueAdapterInterface;
use FmLabs\Q\QueueMessageInterface;

/**
 * Class BaseAdapter
 *
 * @package FmLabs\Q\Adapter
 */
abstract class BaseAdapter implements \FmLabs\Q\QueueAdapterInterface
{
    /**
     * @var \FmLabs\Q\QueueMessageInterface::class Message class name
     */
    protected $messageClass = TextMessage::class;

    /**
     * @var array Default adapter configuration
     */
    protected $defaultConfig = [];

    /**
     * @var array Current adapter configuration
     */
    protected $config = [];

    /**
     * BaseAdapter constructor.
     *
     * @param array $config Adapter config
     */
    public function __construct(array $config = [])
    {
        $this->setConfig($config);

        $messageClass = $this->getConfig('messageClass', TextMessage::class);
        $this->setMessageClass($messageClass);
    }

    /**
     * @param array $config Adapter configuration
     * @param bool $merge Merge flag. If FALSE, existing config will be overwritten
     * @return $this
     */
    protected function setConfig(array $config, bool $merge = true)
    {
        if (!$merge) {
            $this->config = $this->defaultConfig;
        }

        $this->config += $config;

        return $this;
    }

    /**
     * @param string $key Config key
     * @param null $default Default value
     * @return mixed|null
     */
    protected function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * @param string $className Message class name
     * @return $this
     * @todo Change method access to protected
     */
    public function setMessageClass(string $className = TextMessage::class)
    {
        $this->messageClass = $className;

        return $this;
    }

//    /**
//     * @param null|mixed $payload Message payload
//     * @return \FmLabs\Q\QueueMessageInterface
//     */
//    public function createMessage($payload = null): QueueMessageInterface
//    {
//        $msg = new $this->_messageClass();
//        if (!($msg instanceof QueueMessageInterface)) {
//            throw new \RuntimeException('Message object does not implement QueueMessageInterface');
//        }
//        if ($payload !== null) {
//            $msg->setPayload($payload);
//        }
//
//        return $msg;
//    }

    /**
     * @param string $body String to restore message from
     * @return \FmLabs\Q\QueueMessageInterface
     */
    public function restoreMessage(string $body): QueueMessageInterface
    {
        $msg = new $this->messageClass();
        if (!($msg instanceof QueueMessageInterface)) {
            throw new \RuntimeException('Message object does not implement QueueMessageInterface');
        }
        $msg->unserialize($body);

        return $msg;
    }

    /**
     * @param string $msg Log message
     * @return void
     * @todo Use LoggerInterface
     */
    protected function log(string $msg): void
    {
        echo $msg, "\n";
    }
}
