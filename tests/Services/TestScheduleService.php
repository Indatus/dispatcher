<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\CommandScheduler\Services\ScheduleService;
use Indatus\CommandScheduler\Table;
use Indatus\CommandScheduler\Scheduler;

class TestScheduleService extends TestCase
{
    /**
     * @var Indatus\CommandScheduler\ScheduleService
     */
    private $scheduleService;

    public function setUp()
    {
        parent::setUp();

        $this->scheduleService = new ScheduleService(new Table());
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetScheduledCommands()
    {
        $scheduledCommands = [$class = m::mock('Indatus\CommandScheduler\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\CommandScheduler\Schedulable'));
            })];

        Artisan::shouldReceive('all')->once()->andReturn($scheduledCommands);

        $this->assertSameSize($scheduledCommands, $this->scheduleService->getScheduledCommands());
    }

    /**
     * Test that a summary is properly generated
     */
    public function testPrintSummary()
    {
        $m = m::mock('Indatus\CommandScheduler\Table', function ($m) {
                $m->shouldReceive('setHeaders')->once();
                $m->shouldReceive('sort')->once();
                $m->shouldReceive('display')->once();
            });
        $scheduleService = new ScheduleService($m);
        $scheduleService->printSummary();
    }

} 