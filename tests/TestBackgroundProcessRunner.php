<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use \Indatus\CommandScheduler\BackgroundProcessRunner;

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
        $commandService = m::mock('Indatus\CommandScheduler\Services\CommandService', function ($m) {
                $m->shouldReceive('getRunCommand')->once()->andReturn('echo "this is a test"');
            });
        $scheduledCommand = m::mock('Indatus\CommandScheduler\ScheduledCommand');

        $backgroundProcessRunner = new BackgroundProcessRunner($commandService);
        $this->assertTrue($backgroundProcessRunner->run($scheduledCommand));
    }
} 