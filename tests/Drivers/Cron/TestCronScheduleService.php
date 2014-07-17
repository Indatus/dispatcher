<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\Drivers\Cron\ScheduleService;
use Mockery as m;

class TestCronScheduleService extends TestCase
{
    /**
     * @var Indatus\Dispatcher\ScheduleService
     */
    private $scheduleService;

    public function setUp()
    {
        parent::setUp();

        $table = m::mock('Indatus\Dispatcher\Table');
        $this->app->instance('Indatus\Dispatcher\Table', $table);

        $this->scheduleService = new ScheduleService();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testIsNotDue()
    {
        $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable', function ($m) {
            //schedule the cron to run yesterday
            $dateTime = new DateTime('yesterday');
            $m->shouldReceive('getSchedule')->once()->andReturn('* * * * '.$dateTime->format('N'));
        });
        $this->assertFalse($this->scheduleService->isDue($scheduler));
    }

    public function testIsDueException()
    {
        Log::shouldReceive('error')->once();
        $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable', function ($m) {
                $m->shouldReceive('getSchedule')->once()->andReturn('asdf');
            });
        $this->assertFalse($this->scheduleService->isDue($scheduler));
    }

    public function testIsDue()
    {
        $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable', function ($m) {
                $m->shouldReceive('getSchedule')->once()->andReturn('* * * * *');
            });
        $this->assertTrue($this->scheduleService->isDue($scheduler));
    }

    /**
     * Test that a summary is properly generated
     * Dangit this is ugly... gotta find a new cli library
     */
    public function testPrintSummary()
    {
        $table = m::mock('Indatus\Dispatcher\Table', function ($m) {
                $m->shouldReceive('setHeaders')->once();
                $m->shouldReceive('display')->once();
            });
        $queue = m::mock('Indatus\Dispatcher\Queue');

        $scheduledCommandWithMultipleSchedulers = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) use ($table) {
                $table->shouldReceive('addRow')->times(3);

                $scheduler = m::mock('Indatus\Dispatcher\Drivers\Cron\Scheduler', function ($m) {
                        $m->shouldReceive('getScheduleMinute');
                        $m->shouldReceive('getScheduleHour');
                        $m->shouldReceive('getScheduleDayOfMonth');
                        $m->shouldReceive('getScheduleMonth');
                        $m->shouldReceive('getScheduleDayOfWeek');
                        $m->shouldReceive('getArguments')->twice()->andReturn(array());
                        $m->shouldReceive('getOptions')->twice()->andReturn(array());
                    });

                $m->shouldReceive('getName')->once();
                $m->shouldReceive('user')->once();
                $m->shouldReceive('environment')->twice();
                $m->shouldReceive('schedule')->once()->andReturn(array(
                        $scheduler,
                        $scheduler
                    ));
            });
        $scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) use ($table) {
                $table->shouldReceive('addRow')->once();

                $scheduler = m::mock('Indatus\Dispatcher\Drivers\Cron\Scheduler', function ($m) {
                        $m->shouldReceive('getScheduleMinute');
                        $m->shouldReceive('getScheduleHour');
                        $m->shouldReceive('getScheduleDayOfMonth');
                        $m->shouldReceive('getScheduleMonth');
                        $m->shouldReceive('getScheduleDayOfWeek');
                    });

                $m->shouldReceive('getName')->once();
                $m->shouldReceive('user')->once();
                $m->shouldReceive('environment')->twice();
                $m->shouldReceive('schedule')->once()->andReturn($scheduler);
            });
        $this->app->instance('Indatus\Dispatcher\Table', $table);
        $scheduleService = m::mock('Indatus\Dispatcher\Drivers\Cron\ScheduleService[getScheduledCommands]',
            array(),
            function ($m) use ($scheduledCommand, $scheduledCommandWithMultipleSchedulers) {
                $m->shouldReceive('getScheduledCommands')->once()->andReturn(array(
                        $scheduledCommandWithMultipleSchedulers,
                        $scheduledCommand
                    ));
            });
        $scheduleService->printSummary();
    }
} 