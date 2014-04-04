<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\Queue;

class TestQueue extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testQueue()
    {
        $item = m::mock('Indatus\Dispatcher\QueueItem');

        $queue = new Indatus\Dispatcher\Queue();
        $this->assertEquals(0, $queue->size());
        $queue->add($item);
        $this->assertEquals(1, $queue->size());
        $this->assertEquals([$item], $queue->flush());
        $this->assertEquals(0, $queue->size());
    }
} 