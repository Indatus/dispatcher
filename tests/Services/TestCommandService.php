<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\CommandScheduler\Services\CommandService;
use Indatus\CommandScheduler\Services\ScheduleService;
use Indatus\CommandScheduler\Table;
use Indatus\CommandScheduler\Scheduler;

class TestCommandService extends TestCase
{
    /**
     * @var Indatus\CommandScheduler\Services\CommandService
     */
    private $commandService;

    public function setUp()
    {
        parent::setUp();

        $scheduleService = new ScheduleService(new Table());
        $this->commandService = new CommandService($scheduleService);

    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testRunDue()
    {
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('isEnabled')->once()->andReturn(true);

        $scheduleService = m::mock('Indatus\CommandScheduler\Services\ScheduleService');
        $scheduleService->shouldReceive('getDueCommands')->once()->andReturn([$scheduledCommand]);

        $commandService = m::mock('Indatus\CommandScheduler\Services\CommandService[runnableInEnvironment,run]',
            [$scheduleService],
            function ($m) {
                $m->shouldReceive('runnableInEnvironment')->andReturn(true);
                $m->shouldReceive('run')->andReturnNull();
            });

        $this->assertNull($commandService->runDue());
    }

    public function testRun()
    {
        $scheduledCommand = $this->mockCommand();

        $backgroundProcess = m::mock('Indatus\ScheduledCommand\BackgroundProcess');
        $backgroundProcess->shouldReceive('run')->once();
        $this->app->instance('Indatus\ScheduledCommand\BackgroundProcess', $backgroundProcess);

        $this->assertNull($this->commandService->run($scheduledCommand));
    }

    public function testRunnableInAnyEnvironment()
    {
        $scheduledCommand = $this->mockCommand('*');

        App::shouldReceive('environment')->andReturn('*');
        $this->assertTrue($this->commandService->runnableInEnvironment($scheduledCommand));
    }

    public function testRunnableInOneEnvironment()
    {
        $scheduledCommand = $this->mockCommand('local');

        App::shouldReceive('environment')->andReturn('local');
        $this->assertTrue($this->commandService->runnableInEnvironment($scheduledCommand));
    }

    public function testRunnableInMultipleEnvironments()
    {
        $scheduledCommand = $this->mockCommand(['local', 'development']);

        App::shouldReceive('environment')->andReturn('local');
        $this->assertTrue($this->commandService->runnableInEnvironment($scheduledCommand));
    }

    private function mockCommand ($environment = '*')
    {
        return $class = m::mock('Indatus\CommandScheduler\ScheduledCommand', function ($m) use ($environment) {
                $m->shouldReceive('environment')->andReturn($environment);
            });
    }

} 