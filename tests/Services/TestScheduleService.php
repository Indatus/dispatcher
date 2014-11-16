<?php namespace Indatus\Dispatcher\Services;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Artisan;
use Indatus\Dispatcher\Table;
use Mockery as m;
use TestCase;

class TestScheduleService extends TestCase
{
    /**
     * @var ScheduleService
     */
    private $scheduleService;

    public function setUp()
    {
        parent::setUp();

        $this->scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService[isDue]');

        $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');
        $this->app->instance('Indatus\Dispatcher\Scheduling\Schedulable', $scheduler);
    }

    public function testGetScheduledCommands()
    {
        $scheduledCommands = [$class = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Scheduling\Schedulable'));
            })];

        Artisan::shouldReceive('all')->once()->andReturn($scheduledCommands);

        $this->assertSameSize($scheduledCommands, $this->scheduleService->getScheduledCommands());
    }

    public function testGetQueue()
    {
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService[getScheduledCommands,isDue]', [
                new Table()
            ], function ($m) {
                $command = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand');
                $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');
                $command->shouldReceive('schedule')->once()
                    ->andReturn($scheduler);

                $m->shouldReceive('getScheduledCommands')->once()
                    ->andReturn([$command]);
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
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService[getScheduledCommands,isDue]', [
                new Table()
            ], function ($m) use (&$command) {
                $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');
                $command->shouldReceive('schedule')->once()
                    ->andReturn($scheduler);

                $m->shouldReceive('getScheduledCommands')->once()
                    ->andReturn([$command]);
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
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService[getScheduledCommands,isDue]', [
                new Table()
            ], function ($m) {
                $command = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand');
                $command->shouldReceive('schedule')->once()
                    ->andReturn([1]);
                $command->shouldReceive('getName')->once()
                    ->andReturn('asdf');

                $m->shouldReceive('getScheduledCommands')->once()
                    ->andReturn([$command]);
            });

        $queue = $scheduleService->getQueue(m::mock('Indatus\Dispatcher\Debugger'));
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Queue',
            $queue
        );
    }
}
