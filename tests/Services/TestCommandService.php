<?php namespace Indatus\Dispatcher\Services;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use App;
use Mockery as m;
use TestCase;

class TestCommandService extends TestCase
{
    /**
     * @var \Indatus\Dispatcher\Services\CommandService
     */
    private $commandService;

    public function setUp()
    {
        parent::setUp();

        $scheduleService = m::mock('Indatus\Dispatcher\Drivers\DateTime\ScheduleService');
        $scheduleService->shouldDeferMissing();
        $this->commandService = new CommandService($scheduleService);

        //default all commands to unix
        $this->app->instance('Indatus\Dispatcher\Platform', m::mock('Indatus\Dispatcher\Platform', function ($m) {
                    $m->shouldReceive('isUnix')->andReturn(true);
                    $m->shouldReceive('isWindows')->andReturn(false);
                    $m->shouldReceive('isHHVM')->andReturn(false);
                }));
    }

    public function testRunDue()
    {
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('isEnabled')->once()->andReturn(true);
        $scheduledCommand->shouldReceive('getName')->once()->andReturn('test:command');
        $scheduledCommand->shouldReceive('user')->once()->andReturn(false);

        $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');
        $scheduler->shouldReceive('getArguments')->once()->andReturn([]);
        $scheduler->shouldReceive('getOptions')->once()->andReturn([]);
        $queue = m::mock('Indatus\Dispatcher\Queue',
            function ($m) use ($scheduledCommand, $scheduler) {
                $item = m::mock('Indatus\Dispatcher\QueueItem');
                $item->shouldReceive('getCommand')->once()->andReturn($scheduledCommand);
                $item->shouldReceive('getScheduler')->once()->andReturn($scheduler);
                $m->shouldReceive('flush')->once()->andReturn([$item]);
            });
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService');
        $scheduleService->shouldReceive('getQueue')->once()->andReturn($queue);
        $this->app->instance('Indatus\Dispatcher\Services\ScheduleService', $scheduleService);

        $commandService = m::mock('Indatus\Dispatcher\Services\CommandService[runnableInEnvironment,run]',
            [$scheduleService],
            function ($m) use ($scheduledCommand, $scheduler) {
                $m->shouldReceive('runnableInEnvironment')->andReturn(true);
                $m->shouldReceive('run')->with($scheduledCommand, $scheduler)->andReturnNull();
            });

        $debugger = m::mock('Indatus\Dispatcher\Debugger');
        $debugger->shouldReceive('log');
        $debugger->shouldReceive('commandRun');

        $this->assertNull($commandService->runDue($debugger));
    }

    public function testLogDisabled()
    {
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('isEnabled')->once()->andReturn(false);

        $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');
        $queue = m::mock('Indatus\Dispatcher\Queue',
            function ($m) use ($scheduledCommand, $scheduler) {
                $item = m::mock('Indatus\Dispatcher\QueueItem');
                $item->shouldReceive('getCommand')->once()->andReturn($scheduledCommand);
                $m->shouldReceive('flush')->once()->andReturn([$item]);
            });
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService');
        $scheduleService->shouldReceive('getQueue')->once()->andReturn($queue);
        $this->app->instance('Indatus\Dispatcher\Services\ScheduleService', $scheduleService);

        $commandService = m::mock('Indatus\Dispatcher\Services\CommandService[runnableInEnvironment,run]',
            [$scheduleService]);

        $debugger = m::mock('Indatus\Dispatcher\Debugger');
        $debugger->shouldReceive('commandNotRun')->once()->with($scheduledCommand, 'Command is disabled');
        $debugger->shouldReceive('log');
        $debugger->shouldReceive('commandRun');

        $this->assertNull($commandService->runDue($debugger));
    }

    public function testLogNotRunnableInMaintenanceMode()
    {
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('isEnabled')->once()->andReturn(true);

        $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');
        $queue = m::mock('Indatus\Dispatcher\Queue',
            function ($m) use ($scheduledCommand, $scheduler) {
                $item = m::mock('Indatus\Dispatcher\QueueItem');
                $item->shouldReceive('getCommand')->once()->andReturn($scheduledCommand);
                $m->shouldReceive('flush')->once()->andReturn([$item]);
            });
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService');
        $scheduleService->shouldReceive('getQueue')->once()->andReturn($queue);
        $this->app->instance('Indatus\Dispatcher\Services\ScheduleService', $scheduleService);

        $commandService = m::mock('Indatus\Dispatcher\Services\CommandService[runnableInCurrentMaintenanceSetting,run]',
            [$scheduleService],
            function ($m) use ($scheduledCommand, $scheduler) {
                $m->shouldReceive('runnableInCurrentMaintenanceSetting')->andReturn(false);
            });

        $debugger = m::mock('Indatus\Dispatcher\Debugger');
        $debugger->shouldReceive('commandNotRun')->once()->with($scheduledCommand, 'Command is not configured to run while application is in maintenance mode');
        $debugger->shouldReceive('log');
        $debugger->shouldReceive('commandRun');

        $this->assertNull($commandService->runDue($debugger));
    }

    public function testLogNotRunnableInEnvironment()
    {
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('isEnabled')->once()->andReturn(true);

        $scheduler = m::mock('Indatus\Dispatcher\Scheduling\Schedulable');
        $queue = m::mock('Indatus\Dispatcher\Queue',
            function ($m) use ($scheduledCommand, $scheduler) {
                $item = m::mock('Indatus\Dispatcher\QueueItem');
                $item->shouldReceive('getCommand')->once()->andReturn($scheduledCommand);
                $m->shouldReceive('flush')->once()->andReturn([$item]);
            });
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService');
        $scheduleService->shouldReceive('getQueue')->once()->andReturn($queue);
        $this->app->instance('Indatus\Dispatcher\Services\ScheduleService', $scheduleService);

        $commandService = m::mock('Indatus\Dispatcher\Services\CommandService[runnableInEnvironment,run]',
            [$scheduleService],
            function ($m) use ($scheduledCommand, $scheduler) {
                $m->shouldReceive('runnableInEnvironment')->andReturn(false);
            });

        $debugger = m::mock('Indatus\Dispatcher\Debugger');
        $debugger->shouldReceive('commandNotRun')->once()->with($scheduledCommand, 'Command is not configured to run in '.App::environment());
        $debugger->shouldReceive('log');
        $debugger->shouldReceive('commandRun');

        $this->assertNull($commandService->runDue($debugger));
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

    public function testNotRunnableInCurrentMaintenanceSetting()
    {
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('runInMaintenanceMode')->once()->andReturn(false);

        App::shouldReceive('isDownForMaintenance')->andReturn(true);
        $this->assertFalse($this->commandService->runnableInCurrentMaintenanceSetting($scheduledCommand));
    }

    public function testRunnableInCurrentMaintenanceSetting()
    {
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('runInMaintenanceMode')->once()->andReturn(true);

        App::shouldReceive('isDownForMaintenance')->andReturn(true);
        $this->assertTrue($this->commandService->runnableInCurrentMaintenanceSetting($scheduledCommand));
    }

    public function testChecksForMaintenanceMode()
    {
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('runInMaintenanceMode')->never();

        App::shouldReceive('isDownForMaintenance')->andReturn(false);
        $this->assertTrue($this->commandService->runnableInCurrentMaintenanceSetting($scheduledCommand));
    }

    public function testPrepareArguments()
    {
        $arguments = [
            'argument',
        ];
        $commandService = $this->getMockForAbstractClass('Indatus\Dispatcher\Services\CommandService',
            [
                m::mock('Indatus\Dispatcher\Services\ScheduleService'),
            ]);

        $this->assertEquals(
            'argument',
            $commandService->prepareArguments($arguments)
        );
    }

    public function testPrepareOptions()
    {
        $arguments = [
            'test' => 'argument',
            'keyOnly',
        ];
        $commandService = $this->getMockForAbstractClass('Indatus\Dispatcher\Services\CommandService',
            [
                m::mock('Indatus\Dispatcher\Services\ScheduleService'),
            ]);

        $this->assertEquals(
            '--test="argument" --keyOnly',
            $commandService->prepareOptions($arguments)
        );
    }

    public function testPrepareOptionsArrayValue()
    {
        $arguments = [
            'test' => 'argument',
            'option' => [
                'value1',
                'value2',
            ],
        ];
        $commandService = $this->getMockForAbstractClass('Indatus\Dispatcher\Services\CommandService',
            [
                m::mock('Indatus\Dispatcher\Services\ScheduleService'),
            ]);

        $this->assertEquals(
            '--test="argument" --option="value1" --option="value2"',
            $commandService->prepareOptions($arguments)
        );
    }

    public function testGetRunCommand()
    {
        $commandName = 'test:command';
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('getName')->andReturn($commandName);
        $scheduledCommand->shouldReceive('user')->andReturn(false);
        $this->assertEquals($this->commandService->getRunCommand($scheduledCommand), implode(' ', [
                    PHP_BINARY,
                    base_path().'/artisan',
                    $commandName,
                    '--env='.App::environment(),
                    '> /dev/null',
                    '&',
                ]));
    }

    public function testGetRunCommandWindows()
    {
        $this->app->instance('Indatus\Dispatcher\Platform', m::mock('Indatus\Dispatcher\Platform', function ($m) {
                    $m->shouldReceive('isUnix')->andReturn(false);
                    $m->shouldReceive('isWindows')->andReturn(true);
                    $m->shouldReceive('isHHVM')->andReturn(false);
                }));

        $commandName = 'test:command';
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('getName')->andReturn($commandName);
        $scheduledCommand->shouldReceive('user')->andReturn(false);
        $this->assertEquals($this->commandService->getRunCommand($scheduledCommand), implode(' ', [
                    'START',
                    '/B',
                    PHP_BINARY,
                    base_path().'/artisan',
                    $commandName,
                    '--env='.App::environment(),
                    '> NULL',
                ]));
    }

    public function testGetRunCommandHHVM()
    {
        $this->app->instance('Indatus\Dispatcher\Platform', m::mock('Indatus\Dispatcher\Platform', function ($m) {
                    $m->shouldReceive('isUnix')->andReturn(true);
                    $m->shouldReceive('isWindows')->andReturn(false);
                    $m->shouldReceive('isHHVM')->once()->andReturn(true);
                }));

        $commandName = 'test:command';
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('getName')->andReturn($commandName);
        $scheduledCommand->shouldReceive('user')->andReturn(false);
        $this->assertEquals($this->commandService->getRunCommand($scheduledCommand), implode(' ', [
                    '/usr/bin/env',
                    'hhvm',
                    base_path().'/artisan',
                    $commandName,
                    '--env='.App::environment(),
                    '> /dev/null',
                    '&',
                ]));
    }

    public function testGetRunCommandWithArguments()
    {
        $commandName = 'test:command';
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('getName')->andReturn($commandName);
        $scheduledCommand->shouldReceive('user')->andReturn(false);
        $this->assertEquals(
            $this->commandService->getRunCommand(
                $scheduledCommand,
                [
                    'option',
                ]
            ),
            implode(' ', [
                    PHP_BINARY,
                    base_path().'/artisan',
                    $commandName,
                    'option',
                    '--env='.App::environment(),
                    '> /dev/null',
                    '&',
                ]));
    }

    public function testGetRunCommandWithOptions()
    {
        $commandName = 'test:command';
        $scheduledCommand = $this->mockCommand();
        $scheduledCommand->shouldReceive('getName')->andReturn($commandName);
        $scheduledCommand->shouldReceive('user')->andReturn(false);
        $this->assertEquals(
            $this->commandService->getRunCommand(
                $scheduledCommand,
                [],
                [
                    'option' => 'value',
                    'anotherOption',
                ]
            ),
            implode(' ', [
                    PHP_BINARY,
                    base_path().'/artisan',
                    $commandName,
                    '--option="value"',
                    '--anotherOption',
                    '--env='.App::environment(),
                    '> /dev/null',
                    '&',
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
                    PHP_BINARY,
                    base_path().'/artisan',
                    $commandName,
                    '--env='.App::environment(),
                    '> /dev/null',
                    '&',
                ]));
    }

    private function mockCommand($environment = '*')
    {
        return $class = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) use ($environment) {
                $m->shouldReceive('environment')->andReturn($environment);
            });
    }
}
