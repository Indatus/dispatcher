<?php namespace Indatus\Dispatcher;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use TestCase;

class TestQueue extends TestCase
{
    public function testQueue()
    {
        $item = m::mock('Indatus\Dispatcher\QueueItem');

        $queue = new Queue();
        $this->assertEquals(0, $queue->size());
        $queue->add($item);
        $this->assertEquals(1, $queue->size());
        $this->assertEquals([$item], $queue->flush());
        $this->assertEquals(0, $queue->size());
    }
}
