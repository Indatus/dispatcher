<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\LaravelCommandScheduler\ScheduleService;
use \Orchestra\Testbench\TestCase;
use Mockery as m;
use Indatus\LaravelCommandScheduler\Scheduler;

class TestScheduleService extends TestCase
{
    /**
     * @var Indatus\LaravelCommandScheduler\ScheduleService
     */
    private $scheduleService;

    public function setUp()
    {
        $this->scheduleService = new ScheduleService();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetScheduledCommands()
    {
        $this->assertSameSize([], $this->scheduleService->getScheduledCommands());

        $service = m::mock('Indatus\LaravelCommandScheduler\ScheduleService', function($m) {
                $class = m::mock('Indatus\LaravelCommandScheduler\ScheduledCommand', function ($m) {
                        $schedule = new Scheduler();
                        $schedule->yearly();
                        $m->shouldReceive('schedule')->andReturn($schedule);
                    });
                $m->shouldReceive('getScheduledCommands')->andReturn([$class]);
            });

        $this->assertSameSize([''], $service->getScheduledCommands());
    }

    /**
     * Test that a summary is properly generated
     */
    /*public function testGetSummary()
    {
        $this->assertEquals($this->scheduleService->getSchedule(), '');
    }*/

} 