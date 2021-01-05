<?php
declare(strict_types=1);

namespace FmLabs\Q\Adapter;

use FmLabs\Q\QueueMessageInterface;

class MemoryAdapter extends BaseAdapter implements \Countable
{
    protected $items = [];

    /**
     * @inheritDoc
     */
    public function push(QueueMessageInterface $msg): void
    {
        array_push($this->items, $msg);
    }

    /**
     * @inheritDoc
     */
    public function pop(): ?QueueMessageInterface
    {
        return array_shift($this->items);
    }

    /**
     * Count number of queued items.
     *
     * @link https://php.net/manual/en/countable.count.php
     * @return int Number of queued items
     */
    public function count()
    {
        return count($this->items);
    }
}
