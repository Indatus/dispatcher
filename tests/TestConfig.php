<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Orchestra\Testbench\TestCase;
use Mockery as m;
use Indatus\CommandScheduler\Scheduler;

/*class TestConfig extends TestCase
{

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testDefaultDriver()
    {
        var_dump(Config::get('indatus-command-scheduler::driver'));
        exit;
        $this->assertEquals(Config::get('indatus-command-scheduler::driver'), 'cron');
    }

} */