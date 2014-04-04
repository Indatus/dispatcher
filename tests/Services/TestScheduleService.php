<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\Drivers\Cron\ScheduleService;
use Indatus\Dispatcher\Scheduler;
use Indatus\Dispatcher\Table;

class TestScheduleService extends TestCase
{
    /**
     * @var Indatus\Dispatcher\ScheduleService
     */
    private $scheduleService;

    public function setUp()
    {
        parent::setUp();

        $this->scheduleService = new ScheduleService(new Table());
    }

    public function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    public function testGetScheduledCommands()
    {
        $scheduledCommands = array($class = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Schedulable'));
            }));

        Artisan::shouldReceive('all')->once()->andReturn($scheduledCommands);

        $this->assertSameSize($scheduledCommands, $this->scheduleService->getScheduledCommands());
    }

    public function testGetDueCommands()
    {
        $scheduleService = m::mock('Indatus\Dispatcher\Drivers\Cron\ScheduleService[getScheduledCommands,isDue]', array(
                new Table()
            ), function ($m) {
                $m->shouldReceive('getScheduledCommands')->once()
                    ->andReturn(array(m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand')));
                $m->shouldReceive('isDue')->once()->andReturn(true);

            });

        $this->assertEquals(1, count($scheduleService->getDueCommands()));
    }

    public function testIsNotDue()
    {
        $scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Scheduling\Schedulable', function ($m) {
                            //schedule the cron to run yesterday
                            $dateTime = new DateTime('yesterday');
                            $m->shouldReceive('getSchedule')->once()->andReturn('* * * * '.$dateTime->format('N'));
                        }));
            });
        $scheduledCommand->shouldReceive('getName');
        $this->assertFalse($this->scheduleService->isDue($scheduledCommand));
    }

    public function testIsDue()
    {
        $scheduledCommand = m::mock('Indatus\Dispatcher\Scheduling\ScheduledCommand', function ($m) {
                $m->shouldReceive('schedule')->andReturn(m::mock('Indatus\Dispatcher\Scheduling\Schedulable', function ($m) {
                        $m->shouldReceive('getSchedule')->once()->andReturn('* * * * *');
                    }));
            });
        $scheduledCommand->shouldReceive('getName');
        $this->assertTrue($this->scheduleService->isDue($scheduledCommand));
    }

} 