<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\CommandScheduler\Drivers\Cron\Scheduler;
use Indatus\CommandScheduler\BackgroundProcess;

class TestBackgroundProcess extends TestCase
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

    public function testSomething()
    {

    }

    /*public function testRunAsOnWindows()
    {
        $platform = m::mock('Indatus\CommandScheduler\Platform');
        $platform->shouldReceive('isWindows')->once()->andReturn(false);
        $this->app->instance('Indatus\CommandScheduler\Platform', $platform);

        $backgroundProcess = new BackgroundProcess();
        //$backgroundProcess->run();
    }*/
} 