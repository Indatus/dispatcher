<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\Scheduler;
use Indatus\Dispatcher\ConfigResolver;

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
        $this->assertInstanceOf('Indatus\Dispatcher\Drivers\Cron\Scheduler', $resolver->resolveDriverClass('Scheduler'));
    }

    public function testLoadingCustomDriver()
    {
        Config::shouldReceive('get')->andReturn('Indatus\Dispatcher\Drivers\Cron');
        $resolver = new ConfigResolver();
        $this->assertInstanceOf('Indatus\Dispatcher\Schedulable', $resolver->resolveDriverClass('Scheduler'));
    }

}