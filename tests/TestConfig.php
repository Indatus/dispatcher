<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\CommandScheduler\Scheduler;

class TestConfig extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testDefaultDriver()
    {
        $this->assertEquals(Config::get('command-scheduler::driver'), 'cron');
    }

    public function testDefaultSchedulerClass()
    {
        $serviceProvider = App::make('Indatus\CommandScheduler\Schedulable');
        $this->assertInstanceOf('Indatus\CommandScheduler\Drivers\Cron\Scheduler', $serviceProvider);
    }

}