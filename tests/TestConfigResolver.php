<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\ConfigResolver;
use Indatus\Dispatcher\Scheduler;
use Mockery as m;

class TestConfigResolver extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testLoadingSchedulerPackagedDriver()
    {
        $resolver = new ConfigResolver();
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Drivers\Cron\Scheduler',
            $resolver->resolveSchedulerClass()
        );
    }

    public function testLoadingServiceCustomDriver()
    {
        Config::shouldReceive('get')->andReturn('cron');
        $resolver = new ConfigResolver();
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Scheduling\Schedulable',
            $resolver->resolveSchedulerClass()
        );
    }

    public function testLoadingServicePackagedDriver()
    {
        $resolver = new ConfigResolver();
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Services\ScheduleService',
            $resolver->resolveServiceClass()
        );
    }

    public function testLoadingSchedulerCustomDriver()
    {
        Config::shouldReceive('get')->andReturn('cron');
        $resolver = new ConfigResolver();
        $this->assertInstanceOf(
            'Indatus\Dispatcher\Services\ScheduleService',
            $resolver->resolveServiceClass()
        );
    }

}