<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\Commands\Run;
use Mockery as m;

class TestRun extends TestCase
{

    /**
     * @var Indatus\Dispatcher\Commands\Run
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
        $commandService = m::mock('Indatus\Dispatcher\Services\CommandService', function ($m) {
                $m->shouldReceive('runDue')->once();
            });

        $command = m::mock('Indatus\Dispatcher\Commands\Run[option]', array(
            $commandService
            ));
        $command->shouldReceive('option')->andReturn(array());
        $command->run(
            m::mock('Symfony\Component\Console\Input\InputInterface')->shouldIgnoreMissing(),
            m::mock('Symfony\Component\Console\Output\OutputInterface')->shouldIgnoreMissing()
        );
    }

} 