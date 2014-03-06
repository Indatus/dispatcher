<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
use Indatus\Dispatcher\BackgroundProcess;

class TestBackgroundProcess extends TestCase
{
    /**
     * @var Indatus\Dispatcher\ScheduledCommand
     */
    private $scheduledCommand;

    public function setUp()
    {
        parent::setUp();

        $this->scheduledCommand = m::mock('Indatus\Dispatcher\ScheduledCommand[schedule]');

        $this->app->instance('Indatus\Dispatcher\Schedulable', new Scheduler());
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
        $platform = m::mock('Indatus\Dispatcher\Platform');
        $platform->shouldReceive('isWindows')->once()->andReturn(false);
        $this->app->instance('Indatus\Dispatcher\Platform', $platform);

        $backgroundProcess = new BackgroundProcess();
        //$backgroundProcess->run();
    }*/
} 