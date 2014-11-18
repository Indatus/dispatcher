<?php namespace Indatus\Dispatcher;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use App;
use Config;
use TestCase;

class TestConfigResolver extends TestCase
{
    /** @var ConfigResolver */
    protected $configResolver;

    public function setUp()
    {
        parent::setUp();
        $this->configResolver = App::make('Indatus\Dispatcher\ConfigResolver');
    }

    public function testLoadingSchedulerPackagedDriver()
    {
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Drivers\DateTime\Scheduler',
            $this->configResolver->resolveSchedulerClass()
        );
    }

    public function testLoadingServiceCustomDriver()
    {
        Config::shouldReceive('get')->andReturn('dateTime')->once();
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Scheduling\Schedulable',
            $this->configResolver->resolveSchedulerClass()
        );
    }

    public function testLoadingServicePackagedDriver()
    {
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Services\ScheduleService',
            $this->configResolver->resolveServiceClass()
        );
    }

    public function testLoadingSchedulerCustomDriver()
    {
        Config::shouldReceive('get')->andReturn('dateTime')->once();
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Services\ScheduleService',
            $this->configResolver->resolveServiceClass()
        );
    }

    public function testDriverCasing()
    {
        $this->assertEquals('DateTime', $this->configResolver->getDriver());
    }
}
