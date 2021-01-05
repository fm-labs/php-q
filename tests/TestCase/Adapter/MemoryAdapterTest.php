<?php
declare(strict_types=1);

namespace FmLabs\Q\Test\TestCase\Adapter;

use FmLabs\Q\Adapter\MemoryAdapter;
use FmLabs\Q\QueueAdapterInterface;

/**
 * Class MemoryAdapterTest
 *
 * @package FmLabs\Q\Test\TestCase\Adapter
 * @group adapter
 * @group memory
 */
class MemoryAdapterTest extends SharedAdapterTest
{
    /**
     * @return \FmLabs\Q\QueueAdapterInterface
     */
    public function getAdapter(): QueueAdapterInterface
    {
        return new MemoryAdapter();
    }
}
