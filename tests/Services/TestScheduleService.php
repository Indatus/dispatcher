<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\Drivers\Cron\ScheduleService;
use Indatus\Dispatcher\Table;
use Indatus\Dispatcher\Scheduler;

class TestScheduleService extends TestCase
{
    /**
     * @var Indatus\Dispatcher\ScheduleService
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
        $scheduledCommands = [$class = m::mock('Indatus\Dispatcher\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Schedulable'));
            })];

        Artisan::shouldReceive('all')->once()->andReturn($scheduledCommands);

        $this->assertSameSize($scheduledCommands, $this->scheduleService->getScheduledCommands());
    }

    /**
     * Test that a summary is properly generated
     * Dangit this is ugly... gotta find a new cli library
     */
    public function testPrintSummary()
    {
        $table = m::mock('Indatus\Dispatcher\Table', function ($m) {
                $m->shouldReceive('setHeaders')->once();
                $m->shouldReceive('sort')->once();
                $m->shouldReceive('display')->once();
            });
        $scheduledCommand = m::mock('Indatus\Dispatcher\ScheduledCommand', function ($m) use ($table) {
                $table->shouldReceive('addRow')->once();

                $m->shouldReceive('getName')->once();
                $m->shouldReceive('user')->once();
                $m->shouldReceive('environment')->twice();
                $m->shouldReceive('schedule')->once()->andReturn(m::mock('Indatus\Dispatcher\Drivers\Cron\Scheduler', function ($m) {
                            $m->shouldReceive('getScheduleMinute');
                            $m->shouldReceive('getScheduleHour');
                            $m->shouldReceive('getScheduleDayOfMonth');
                            $m->shouldReceive('getScheduleMonth');
                            $m->shouldReceive('getScheduleDayOfWeek');
                        }));
            });
        $scheduleService = m::mock('Indatus\Dispatcher\Drivers\Cron\ScheduleService[getScheduledCommands]', [
                $table
            ], function ($m) use ($scheduledCommand) {
                $m->shouldReceive('getScheduledCommands')->once()->andReturn([
                        $scheduledCommand
                    ]);
            });
        $scheduleService->printSummary();
    }

    public function testGetDueCommands()
    {
        $scheduleService = m::mock('Indatus\Dispatcher\Drivers\Cron\ScheduleService[getScheduledCommands,isDue]', [
                new Table()
            ], function ($m) {
                $m->shouldReceive('getScheduledCommands')->once()
                    ->andReturn([m::mock('Indatus\Dispatcher\ScheduledCommand')]);
                $m->shouldReceive('isDue')->once()->andReturn(true);

            });

        $this->assertEquals(1, count($scheduleService->getDueCommands()));
    }

    public function testIsNotDue()
    {
        $scheduledCommand = m::mock('Indatus\Dispatcher\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Scheduler', function ($m) {
                            //schedule the cron to run yesterday
                            $dateTime = new DateTime('yesterday');
                            $m->shouldReceive('getSchedule')->once()->andReturn('* * * * '.$dateTime->format('N'));
                        }));
            });
        $this->assertFalse($this->scheduleService->isDue($scheduledCommand));
    }

    public function testIsDue()
    {
        $scheduledCommand = m::mock('Indatus\Dispatcher\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Scheduler', function ($m) {
                        $m->shouldReceive('getSchedule')->once()->andReturn('* * * * *');
                    }));
            });
        $this->assertTrue($this->scheduleService->isDue($scheduledCommand));
    }

} 