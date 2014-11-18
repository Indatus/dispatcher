<?php namespace Indatus\Dispatcher\Commands;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use TestCase;

class TestRun extends TestCase
{
    /**
     * @var \Indatus\Dispatcher\Commands\Run
     */
    private $command;

    public function setUp()
    {
        parent::setUp();

        $this->command = new Run(
            m::mock('Indatus\Dispatcher\Services\CommandService'),
            m::mock('Indatus\Dispatcher\Drivers\Cron\ScheduleService')
        );
    }

    public function testName()
    {
        $this->assertEquals('scheduled:run', $this->command->getName());
    }

    public function testDescription()
    {
        $this->assertEquals('Run scheduled commands', $this->command->getDescription());
    }

    public function testFire()
    {
        $commandService = m::mock('Indatus\Dispatcher\Services\CommandService', function ($m) {
                $m->shouldReceive('runDue')->once();
            });

        $command = m::mock('Indatus\Dispatcher\Commands\Run[option]', [
            $commandService
        ]);
        $command->shouldReceive('option')->andReturn([]);
        $command->run(
            m::mock('Symfony\Component\Console\Input\InputInterface')->shouldIgnoreMissing(),
            m::mock('Symfony\Component\Console\Output\OutputInterface')->shouldIgnoreMissing()
        );
    }
}
