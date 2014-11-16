<?php namespace Indatus\Dispatcher\Services;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\Drivers\DateTime\ScheduleService;
use Mockery as m;
use TestCase;

class TestScheduleService extends TestCase
{
    /** @var ScheduleService */
    private $scheduleService;

    /** @var \Mockery\MockInterface */
    private $console;

    public function setUp()
    {
        parent::setUp();

        $this->console = m::mock('Illuminate\Contracts\Console\Kernel');

        $this->scheduleService = new ScheduleService($this->console);
    }

    public function testGetScheduledCommands()
    {
        $scheduledCommands = [$class = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Scheduling\Schedulable'));
            })];

        $this->console->shouldReceive('all')->once()->andReturn($scheduledCommands);

        $this->assertSameSize($scheduledCommands, $this->scheduleService->getScheduledCommands());
    }

    public function testGetQueue()
    {
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService[getScheduledCommands,isDue]', [
                $this->console
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
                $this->console
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
                $this->console
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
