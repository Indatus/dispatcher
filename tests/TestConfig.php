<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\Scheduler;

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
        $serviceProvider = App::make('Indatus\Dispatcher\Schedulable');
        $this->assertInstanceOf('Indatus\Dispatcher\Drivers\Cron\Scheduler', $serviceProvider);
    }

}