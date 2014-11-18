<?php namespace Indatus\Dispatcher\Drivers\DateTime;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use App;
use Exception;
use Mockery as m;
use TestCase;

class TestScheduleService extends TestCase
{
    /** @var ScheduleService */
    private $scheduleService;

    /** @var \Mockery\MockInterface */
    private $interpreter;

    /** @var \Mockery\MockInterface */
    private $logger;

    /** @var \Indatus\Dispatcher\Drivers\DateTime\Scheduler */
    private $scheduler;

    /** @var \Mockery\MockInterface */
    private $console;

    public function setUp()
    {
        parent::setUp();

        $this->scheduler = App::make('Indatus\Dispatcher\Drivers\DateTime\Scheduler');

        $this->interpreter = m::mock('Indatus\Dispatcher\Drivers\DateTime\ScheduleInterpreter');
        $this->interpreter->shouldIgnoreMissing(false);
        $this->app->instance('Indatus\Dispatcher\Drivers\DateTime\ScheduleInterpreter', $this->interpreter);

        $this->logger = m::mock('Illuminate\Contracts\Logging\Log');
        $this->app->instance('Illuminate\Contracts\Logging\Log', $this->logger);

        $this->console = m::mock('Illuminate\Contracts\Console\Kernel');
        $this->scheduleService = new ScheduleService($this->console);
    }

    public function testIsNotDue()
    {
        $this->interpreter->shouldReceive('isDue')->once()->andReturn(false);

        $this->assertFalse($this->scheduleService->isDue($this->scheduler));
    }

    public function testIsDueException()
    {
        $exception = new Exception('uh oh!');

        $this->logger->shouldReceive('error')->once()->with($exception);

        $this->interpreter->shouldReceive('isDue')->once()->andThrow($exception);

        $this->assertFalse($this->scheduleService->isDue($this->scheduler));
    }

    public function testIsDue()
    {
        $this->interpreter->shouldReceive('isDue')->andReturn(true);

        $this->assertTrue($this->scheduleService->isDue($this->scheduler));
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

        $scheduledCommandWithMultipleSchedulers = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) use ($table) {
                $table->shouldReceive('addRow')->times(3);

                $scheduler = m::mock('Indatus\Dispatcher\Drivers\DateTime\Scheduler', function ($m) {
                        $m->shouldReceive('getScheduleMonth');
                        $m->shouldReceive('getScheduleWeek');
                        $m->shouldReceive('getScheduleDayOfMonth');
                        $m->shouldReceive('getScheduleDayOfWeek');
                        $m->shouldReceive('getScheduleHour');
                        $m->shouldReceive('getScheduleMinute');
                        $m->shouldReceive('getArguments')->twice()->andReturn([]);
                        $m->shouldReceive('getOptions')->twice()->andReturn([]);
                    });

                $m->shouldReceive('getName')->once();
                $m->shouldReceive('user')->once();
                $m->shouldReceive('environment')->twice();
                $m->shouldReceive('schedule')->once()->andReturn([
                        $scheduler,
                        $scheduler,
                    ]);
            });
        $scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) use ($table) {
                $table->shouldReceive('addRow')->once();

                $scheduler = m::mock('Indatus\Dispatcher\Drivers\DateTime\Scheduler', function ($m) {
                        $m->shouldReceive('getScheduleMonth');
                        $m->shouldReceive('getScheduleWeek');
                        $m->shouldReceive('getScheduleDayOfMonth');
                        $m->shouldReceive('getScheduleDayOfWeek');
                        $m->shouldReceive('getScheduleHour');
                        $m->shouldReceive('getScheduleMinute');
                    });

                $m->shouldReceive('getName')->once();
                $m->shouldReceive('user')->once();
                $m->shouldReceive('environment')->twice();
                $m->shouldReceive('schedule')->once()->andReturn($scheduler);
            });
        $this->app->instance('Indatus\Dispatcher\Table', $table);
        $scheduleService = m::mock('Indatus\Dispatcher\Drivers\DateTime\ScheduleService[getScheduledCommands]',
            [$this->console],
            function ($m) use ($scheduledCommand, $scheduledCommandWithMultipleSchedulers) {
                $m->shouldReceive('getScheduledCommands')->once()->andReturn([
                        $scheduledCommandWithMultipleSchedulers,
                        $scheduledCommand,
                    ]);
            });
        $scheduleService->printSummary();
    }
}
