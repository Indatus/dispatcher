<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\CommandScheduler\Commands\Make;

class TestMake extends TestCase
{

    /**
     * @var Indatus\CommandScheduler\Commands\Make
     */
    private $make;

    public function setUp()
    {
        parent::setUp();

        $this->app->instance('InputInterface', App::mock('Symfony\Component\Console\Input\InputInterface'));
        $this->app->instance('OutputInterface', App::mock('Symfony\Component\Console\Input\OutputInterface'));

        $this->make = new Make(m::mock('Illuminate\Filesystem\Filesystem'));
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testName()
    {
        $this->assertEquals('scheduled:make', $this->make->getName());
    }

    public function testDescription()
    {
        $this->assertEquals('View a summary of all scheduled artisan commands', $this->scheduleSummary->getDescription());
    }

} 