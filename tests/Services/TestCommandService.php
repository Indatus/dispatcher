<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\CommandScheduler\Services\CommandService;
use Indatus\CommandScheduler\Drivers\Cron\ScheduleService;
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
        $scheduledCommand->shouldReceive('getName')->once()->andReturn('test:command');
        $scheduledCommand->shouldReceive('user')->once()->andReturn(false);

        $scheduleService = m::mock('Indatus\CommandScheduler\Drivers\Cron\ScheduleService');
        $scheduleService->shouldReceive('getDueCommands')->once()->andReturn([$scheduledCommand]);
        $this->app->instance('Indatus\CommandScheduler\Services\ScheduleService', $scheduleService);

        $commandService = m::mock('Indatus\CommandScheduler\Services\CommandService[runnableInEnvironment,run]',
            [$scheduleService],
            function ($m) {
                $m->shouldReceive('runnableInEnvironment')->andReturn(true);
                $m->shouldReceive('run')->andReturnNull();
            });

        $this->assertNull($commandService->runDue());
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

    public function testNotRunnableInEnvironment()
    {
        $scheduledCommand = $this->mockCommand(['local', 'development']);

        App::shouldReceive('environment')->andReturn('amazonAWS');
        $this->assertFalse($this->commandService->runnableInEnvironment($scheduledCommand));
    }

    public function testGetRunCommand()
    {
        $commandName = 'test:command';
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('getName')->andReturn($commandName);
        $scheduledCommand->shouldReceive('user')->andReturn(false);
        $this->assertEquals($this->commandService->getRunCommand($scheduledCommand), implode(' ', [
                    'php',
                    base_path().'/artisan',
                    $commandName
                ]));
    }
    public function testGetRunCommandAsUser()
    {
        $user = 'myUser';
        $commandName = 'test:command';
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('getName')->andReturn($commandName);
        $scheduledCommand->shouldReceive('user')->andReturn($user);
        $this->assertEquals($this->commandService->getRunCommand($scheduledCommand), implode(' ', [
                    'sudo -u '.$user,
                    'php',
                    base_path().'/artisan',
                    $commandName
                ]));
    }

    private function mockCommand ($environment = '*')
    {
        return $class = m::mock('Indatus\CommandScheduler\ScheduledCommand', function ($m) use ($environment) {
                $m->shouldReceive('environment')->andReturn($environment);
            });
    }

} 