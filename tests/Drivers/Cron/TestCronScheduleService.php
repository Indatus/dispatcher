<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;

class TestCronScheduleService extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
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
        $scheduleService = m::mock('Indatus\Dispatcher\Drivers\Cron\ScheduleService[getScheduledCommands]', array(
                $table
            ), function ($m) use ($scheduledCommand) {
                $m->shouldReceive('getScheduledCommands')->once()->andReturn(array(
                        $scheduledCommand
                    ));
            });
        $scheduleService->printSummary();
    }
} 