<?php
declare(strict_types=1);

namespace FmLabs\Q\Test\TestCase\Adapter;

use FmLabs\Q\Message\DataMessage;
use FmLabs\Q\Message\TextMessage;
use FmLabs\Q\QueueAdapterInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class BaseAdapterTest
 *
 * @package FmLabs\Q\Test\TestCase\Adapter
 * @group adapter
 */
abstract class SharedAdapterTest extends TestCase
{
    /**
     * @return \FmLabs\Q\QueueAdapterInterface
     */
    abstract public function getAdapter(): QueueAdapterInterface;

    /**
     * @return void
     */
    protected function wait(): void
    {
    }

    /**
     * @return void
     */
    public function testPushAndPopTextMessage(): void
    {
        $adapter = $this->getAdapter();
        $adapter->push(new TextMessage('hello1'));
        $adapter->push(new TextMessage('hello2'));
        $adapter->push(new TextMessage('hello3'));
        $this->wait();
        $this->assertEquals(3, $adapter->count());

        // using instance
        $msg = $adapter->pop();
        //$msg = new TextMessage();
        //$msg->unserialize($next);
        $this->assertEquals('hello1', $msg->getPayload());
        $this->assertEquals(2, $adapter->count());

        // using static restore
        $next = $adapter->pop();
        $msg = TextMessage::restore($next);
        $this->assertEquals('hello2', $msg->getPayload());
        $this->assertEquals(1, $adapter->count());

        // unhandled
        $adapter->pop();
        $this->assertEquals(0, $adapter->count());
    }

    /**
     * @return void
     */
    public function testPushAndPopDataMessage(): void
    {
        $adapter = $this->getAdapter();
        $adapter->setMessageClass(DataMessage::class);
        $adapter->push(new DataMessage(['foo' => 'bar']));
        $this->wait();
        $this->assertEquals(1, $adapter->count());

        $msg = $adapter->pop();
        $this->assertEquals(['foo' => 'bar'], $msg->getPayload());
        $this->assertEquals(0, $adapter->count());
    }
}
