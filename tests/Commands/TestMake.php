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
    private $command;

    public function setUp()
    {
        parent::setUp();

        $this->command = new Make(m::mock('Illuminate\Filesystem\Filesystem'));
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testName()
    {
        $this->assertEquals('scheduled:make', $this->command->getName());
    }

    public function testDescription()
    {
        $this->assertEquals('Create a new scheduled artisan command', $this->command->getDescription());
    }

} 