<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\CommandScheduler\Scheduler;
use Indatus\CommandScheduler\ConfigResolver;

class TestConfigResolver extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testLoadingPackagedDriver()
    {
        $resolver = new ConfigResolver();
        $this->assertInstanceOf('Indatus\CommandScheduler\Drivers\Cron\Scheduler', $resolver->resolveDriverClass('Scheduler'));
    }

    public function testLoadingCustomDriver()
    {
        Config::shouldReceive('get')->andReturn('Indatus\CommandScheduler\Drivers\Cron');
        $resolver = new ConfigResolver();
        $this->assertInstanceOf('Indatus\CommandScheduler\Schedulable', $resolver->resolveDriverClass('Scheduler'));
    }

}