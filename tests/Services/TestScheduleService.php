<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\Drivers\Cron\ScheduleService;
use Indatus\Dispatcher\Scheduler;
use Indatus\Dispatcher\Table;
use Indatus\Dispatcher\Queue;

class TestScheduleService extends TestCase
{
    /**
     * @var Indatus\Dispatcher\ScheduleService
     */
    private $scheduleService;

    public function setUp()
    {
        parent::setUp();

        $this->scheduleService = new ScheduleService(new Table(), new Queue());
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetScheduledCommands()
    {
        $scheduledCommands = array($class = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Scheduling\Schedulable'));
            }));

        Artisan::shouldReceive('all')->once()->andReturn($scheduledCommands);

        $this->assertSameSize($scheduledCommands, $this->scheduleService->getScheduledCommands());
    }

    public function testGetDueCommands()
    {
        $scheduleService = m::mock('Indatus\Dispatcher\Services\ScheduleService[getScheduledCommands,isDue]', array(
                new Table(),
                new Queue()
            ), function ($m) {
                $m->shouldReceive('getScheduledCommands')->once()
                    ->andReturn(array(m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand')));
                $m->shouldReceive('isDue')->once()->andReturn(true);

            });

        $this->assertEquals(1, count($scheduleService->getDueCommands()));
    }

} 