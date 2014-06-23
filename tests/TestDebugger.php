<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\Scheduler;
use Mockery as m;
use Indatus\Dispatcher\Debugger;

class TestDebugger extends TestCase
{

    protected $output;

    protected $optionReader;

    protected $scheduledCommand;

    protected $commandName = 'my:command';

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function setUp()
    {
        $this->output = m::mock('Symfony\Component\Console\Output\Output');

        $this->optionReader = m::mock('Indatus\Dispatcher\OptionReader');
        $this->optionReader->shouldReceive('isDebugMode')->once()->andReturn(true);

        $this->scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommandInterface');
        $this->scheduledCommand
            ->shouldReceive('getName')
            ->andReturn($this->commandName);

        parent::setUp();
    }

    public function testCommandNotRun()
    {
        $reason = 'Because I said so';
        $this->output
            ->shouldReceive('writeln')
            ->once()
            ->with('     <comment>'.$this->commandName.'</comment>: '.$reason);

        $debugger = new Debugger($this->optionReader, $this->output);

        $debugger->commandNotRun($this->scheduledCommand, $reason);
    }

    public function testCommandRun()
    {
        $runCommand = 'php artisan';
        $this->output
            ->shouldReceive('writeln')
            ->once()
            ->with('     <info>'.$this->commandName.'</info>: '.$runCommand);

        $debugger = new Debugger($this->optionReader, $this->output);

        $debugger->commandRun($this->scheduledCommand, $runCommand);
    }

    public function testLog()
    {
        $message = 'php artisan';
        $this->output
            ->shouldReceive('writeln')
            ->once()
            ->with($message);

        $debugger = new Debugger($this->optionReader, $this->output);

        $debugger->log($message);
    }

}