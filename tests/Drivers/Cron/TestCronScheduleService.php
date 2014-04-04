<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use \Indatus\Dispatcher\Drivers\Cron\ScheduleService;
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
        $queue = m::mock('Indatus\Dispatcher\Queue');
        $this->scheduleService = new ScheduleService($table, $queue);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @expectedException \Indatus\Dispatcher\Scheduling\ScheduleException
     */
    public function testIsDueException()
    {
        $command = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand');
        $command->shouldReceive('schedule')->once()->andReturnNull();
        $command->shouldReceive('getName')->once()->andReturn('testCommand');
        $this->scheduleService->isDue($command);
    }

    public function testIsNotDue()
    {
        $scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Scheduling\Schedulable', function ($m) {
                            //schedule the cron to run yesterday
                            $dateTime = new DateTime('yesterday');
                            $m->shouldReceive('getSchedule')->once()->andReturn('* * * * '.$dateTime->format('N'));
                        }));
            });
        $scheduledCommand->shouldReceive('getName');
        $this->assertFalse($this->scheduleService->isDue($scheduledCommand));
    }

    public function testIsDue()
    {
        $scheduler = m::mock('Indatus\Dispatcher\Drivers\Cron\Scheduler', function ($m) {
                $m->shouldReceive('getSchedule')->once()->andReturn('* * * * *');
            });
        App::shouldReceive('make')->with('Indatus\Dispatcher\Scheduling\Schedulable')
            ->andReturn($scheduler);

        $scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) use ($scheduler) {
                $m->shouldReceive('schedule')->andReturn($scheduler);
            });
        $scheduledCommand->shouldReceive('getName');

        $queueItem = m::mock('Indatus\Dispatcher\QueueItem');
        $queueItem->shouldReceive('setCommand')->with($scheduledCommand)->once();
        $queueItem->shouldReceive('setScheduler')->with($scheduler)->once();
        App::shouldReceive('make')->with('Indatus\Dispatcher\QueueItem')
            ->andReturn($queueItem);

        $table = m::mock('Indatus\Dispatcher\Table');
        $queue = m::mock('Indatus\Dispatcher\Queue');
        $queue->shouldReceive('add')->with($queueItem)->once();
        $scheduleService = new ScheduleService($table, $queue);

        $this->assertTrue($scheduleService->isDue($scheduledCommand));
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
        $queue = m::mock('Indatus\Dispatcher\Queue', function ($m) {
                /*$m->shouldReceive('setHeaders')->once();
                $m->shouldReceive('sort')->once();
                $m->shouldReceive('display')->once();*/
            });
        $scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) use ($table) {
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
                $table,
                $queue
            ), function ($m) use ($scheduledCommand) {
                $m->shouldReceive('getScheduledCommands')->once()->andReturn(array(
                        $scheduledCommand
                    ));
            });
        $scheduleService->printSummary();
    }
} 