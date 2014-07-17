<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\Drivers\Cron\ScheduleService;
use Indatus\Dispatcher\Scheduler;
use Indatus\Dispatcher\Table;
use Mockery as m;

class TestScheduleService extends TestCase
{
    /**
     * @var \Indatus\Dispatcher\ScheduleService
     */
    private $scheduleService;

    public function setUp()
    {
        parent::setUp();

        $this->scheduleService = new ScheduleService();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetScheduledCommands()
    {
        $scheduledCommands = array($class = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Scheduling\Schedulable'));
            }));

        Artisan::shouldReceive('all')->once()->andReturn($scheduledCommands);

        $this->assertSameSize($scheduledCommands, $this->scheduleService->getScheduledCommands());
    }

    public function testGetQueue()
    {
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService[getScheduledCommands,isDue]', array(
                new Table()
            ), function ($m) {
                $command = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand');
                $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');
                $command->shouldReceive('schedule')->once()
                    ->andReturn($scheduler);

                $m->shouldReceive('getScheduledCommands')->once()
                    ->andReturn(array($command));
                $m->shouldReceive('isDue')->once()->with($scheduler)
                    ->andReturn(true);

            });

        $queue = $scheduleService->getQueue(m::mock('Indatus\Dispatcher\Debugger'));
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Queue',
            $queue
        );
    }

    public function testLogNotScheduled()
    {
        $command = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand');
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService[getScheduledCommands,isDue]', array(
                new Table()
            ), function ($m) use (&$command) {
                $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');
                $command->shouldReceive('schedule')->once()
                    ->andReturn($scheduler);

                $m->shouldReceive('getScheduledCommands')->once()
                    ->andReturn(array($command));
                $m->shouldReceive('isDue')->once()->with($scheduler)
                    ->andReturn(false);

            });

        $queue = $scheduleService->getQueue(m::mock('Indatus\Dispatcher\Debugger', function ($m) use (&$command) {
                    $m->shouldReceive('commandNotRun')->with($command, 'No schedules were due');
                }));
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Queue',
            $queue
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetQueueException()
    {
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService[getScheduledCommands,isDue]', array(
                new Table()
            ), function ($m) {
                $command = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand');
                $command->shouldReceive('schedule')->once()
                    ->andReturn(array(1));
                $command->shouldReceive('getName')->once()
                    ->andReturn('asdf');

                $m->shouldReceive('getScheduledCommands')->once()
                    ->andReturn(array($command));
            });

        $queue = $scheduleService->getQueue(m::mock('Indatus\Dispatcher\Debugger'));
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Queue',
            $queue
        );
    }

} 