<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\CommandScheduler\Drivers\Cron\Scheduler;

class TestScheduledCommand extends TestCase
{
    /**
     * @var Indatus\CommandScheduler\ScheduledCommand
     */
    private $scheduledCommand;

    public function setUp()
    {
        parent::setUp();

        $this->scheduledCommand = m::mock('Indatus\CommandScheduler\ScheduledCommand[schedule]');

        $this->app->instance('Indatus\CommandScheduler\Schedulable', new Scheduler());
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testDefaultUser()
    {
        $this->assertFalse($this->scheduledCommand->user());
    }

} 