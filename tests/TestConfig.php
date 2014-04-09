<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\Scheduler;
use Mockery as m;

class TestConfig extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testDefaultDriver()
    {
        $this->assertEquals(Config::get('dispatcher::driver'), 'cron');
    }

    public function testDefaultSchedulerClass()
    {
        $serviceProvider = App::make('Indatus\Dispatcher\Scheduling\Schedulable');
        $this->assertInstanceOf('Indatus\Dispatcher\Drivers\Cron\Scheduler', $serviceProvider);
    }

}