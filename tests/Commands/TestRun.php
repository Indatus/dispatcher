<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\CommandScheduler\Commands\Run;

class TestRun extends TestCase
{

    /**
     * @var Indatus\CommandScheduler\Commands\Run
     */
    private $command;

    public function setUp()
    {
        parent::setUp();

        $this->command = new Run(m::mock('Indatus\CommandScheduler\Services\CommandService'), m::mock('Indatus\CommandScheduler\Drivers\Cron\ScheduleService'));
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
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
        $commandService = m::mock('Indatus\CommandScheduler\Services\CommandService', function ($m) {
                $m->shouldReceive('runDue')->once();
            });
        $command = new Run($commandService);
        $command->fire();
    }

} 