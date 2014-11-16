<?php namespace Indatus\Dispatcher;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use TestCase;

class TestBackgroundProcessRunner extends TestCase
{
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
