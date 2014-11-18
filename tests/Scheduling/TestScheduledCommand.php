<?php namespace Indatus\Dispatcher\Scheduling;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use TestCase;

class TestScheduledCommand extends TestCase
{
    /**
     * @var \Indatus\Dispatcher\Scheduling\ScheduledCommand
     */
    private $scheduledCommand;

    public function setUp()
    {
        parent::setUp();

        $this->scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand[schedule]');
    }

    public function testDefaultUser()
    {
        $this->assertFalse($this->scheduledCommand->user());
    }

    public function testDefaultEnvironment()
    {
        $this->assertEquals('*', $this->scheduledCommand->environment());
    }

    public function testDefaultRunInMaintenanceMode()
    {
        $this->assertFalse($this->scheduledCommand->runInMaintenanceMode());
    }
}
