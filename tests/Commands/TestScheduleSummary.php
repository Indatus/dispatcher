<?php namespace Indatus\Dispatcher\Commands;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use TestCase;

class TestScheduleSummary extends TestCase
{
    /** @var  \Mockery\MockInterface */
    private $scheduleService;

    /** @var  \Indatus\Dispatcher\Commands\ScheduleSummary */
    private $command;

    public function setUp()
    {
        parent::setUp();

        $this->scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService');

        $this->command = new ScheduleSummary($this->scheduleService);
    }

    public function testName()
    {
        $this->assertEquals('scheduled:summary', $this->command->getName());
    }

    public function testDescription()
    {
        $this->assertEquals('View a summary of all scheduled artisan commands', $this->command->getDescription());
    }

    public function testFire()
    {
        $scheduleService = new ScheduleSummary(m::mock('Indatus\Dispatcher\Services\ScheduleService', function ($m) {
                $m->shouldReceive('printSummary')->once();
            }));
        $scheduleService->fire();
    }
}
