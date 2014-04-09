<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\BackgroundProcessRunner;
use Mockery as m;

class TestBackgroundProcessRunner extends TestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testRun()
    {
        $commandService = m::mock('Indatus\Dispatcher\Services\CommandService', function ($m) {
                $m->shouldReceive('getRunCommand')->once()->andReturn('echo "this is a test"');
            });
        $scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand');

        $backgroundProcessRunner = new BackgroundProcessRunner($commandService);
        $this->assertTrue($backgroundProcessRunner->run($scheduledCommand));
    }
} 