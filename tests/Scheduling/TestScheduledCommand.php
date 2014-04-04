<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;

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

        $this->app->instance(
            'Indatus\Dispatcher\Scheduling\Schedulable',
            new Scheduler(App::make('Indatus\Dispatcher\ConfigResolver'))
        );
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

    public function testDefaultEnvironment()
    {
        $this->assertEquals('*', $this->scheduledCommand->environment());
    }

} 