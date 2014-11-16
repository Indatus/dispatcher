<?php namespace Indatus\Dispatcher;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use App;
use Config;
use TestCase;

class TestConfig extends TestCase
{
    public function testDefaultDriver()
    {
        $this->assertEquals(Config::get('dispatcher::driver'), 'dateTime');
    }

    public function testDefaultSchedulerClass()
    {
        $serviceProvider = App::make('Indatus\Dispatcher\Scheduling\Schedulable');
        $this->assertInstanceOf('Indatus\Dispatcher\Drivers\DateTime\Scheduler', $serviceProvider);
    }

    public function testDefaultServiceClass()
    {
        $serviceProvider = App::make('Indatus\Dispatcher\Services\ScheduleService');
        $this->assertInstanceOf('Indatus\Dispatcher\Drivers\DateTime\ScheduleService', $serviceProvider);
    }
}
