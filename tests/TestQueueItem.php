<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\QueueItem;
use Mockery as m;

class TestQueueItem extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testQueue()
    {
        $command = m::mock('Indatus\Dispatcher\Scheduling\Command');
        $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');

        $queueItem = new QueueItem();
        $queueItem->setCommand($command);
        $this->assertEquals($command, $queueItem->getCommand());
        $queueItem->setScheduler($scheduler);
        $this->assertEquals($scheduler, $queueItem->getScheduler());
    }
} 