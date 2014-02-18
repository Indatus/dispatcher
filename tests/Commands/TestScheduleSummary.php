<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\LaravelCommandScheduler\ScheduleService;
use \Orchestra\Testbench\TestCase;
use Mockery as m;
use Indatus\LaravelCommandScheduler\Commands\ScheduleSummary;

class TestScheduleSummary extends TestCase
{

    /** @var  \Mockery\MockInterface */
    private $scheduleService;

    /** @var  \Indatus\LaravelCommandScheduler\Commands\ScheduleSummary */
    private $scheduleSummary;

    public function setUp()
    {
        $this->scheduleService = m::mock('Indatus\LaravelCommandScheduler\ScheduleService');

        $this->scheduleSummary = new ScheduleSummary($this->scheduleService);
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testName()
    {
        $this->assertEquals('crontab:summary', $this->scheduleSummary->getName());
    }

    public function testDescription()
    {
        $this->assertEquals('View a summary for all scheduled artisan commands', $this->scheduleSummary->getDescription());
    }

    public function testFire()
    {
        $this->scheduleService->shouldReceive('getSummary')->once();

        $scheduleService = new ScheduleSummary($this->scheduleService);
        $scheduleService->fire();
    }

} 