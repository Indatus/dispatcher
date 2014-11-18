<?php namespace Indatus\Dispatcher\Drivers\DateTime;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use App;
use Indatus\Dispatcher\Month;
use Mockery as m;
use TestCase;

class TestScheduleInterpreter extends TestCase
{
    /** @var \Indatus\Dispatcher\Drivers\DateTime\ScheduleInterpreter */
    private $interpreter;

    /** @var \Mockery\MockInterface */
    private $cronExpression;

    /** @var \Mockery\MockInterface */
    private $carbon;

    /** @var \Mockery\MockInterface */
    private $scheduler;

    public function setUp()
    {
        parent::setUp();

        $this->carbon = m::mock('Carbon\Carbon');
        $this->carbon->shouldIgnoreMissing();

        $this->cronExpression = m::mock('Cron\CronExpression');
        $this->app->instance('Cron\CronExpression', $this->cronExpression);

        $this->scheduler = m::mock('Indatus\Dispatcher\Drivers\DateTime\Scheduler');
        $this->scheduler->shouldIgnoreMissing();

        $this->interpreter = m::mock('Indatus\Dispatcher\Drivers\DateTime\ScheduleInterpreter[]', [
                $this->scheduler,
                $this->carbon
            ]);
    }

    public function testIsDueWithCron()
    {
        $this->scheduler->shouldReceive('getScheduleWeek')->andReturn(Scheduler::NONE);

        $cronSchedule = 'cronSchedule';
        $this->scheduler->shouldReceive('getCronSchedule')->andReturn($cronSchedule);

        App::shouldReceive('make')->with('Cron\CronExpression', [$cronSchedule])->once()->andReturn($this->cronExpression);

        $returnValue = 'dueStatus';
        $this->cronExpression->shouldReceive('isDue')->once()->andReturn($returnValue);

        $this->assertEquals($returnValue, $this->interpreter->isDue());
    }

    public function testIsDueWithWeeklyAndCron()
    {
        $this->scheduler->shouldReceive('thisWeek')->andReturn(true);
        $this->scheduler->shouldReceive('getScheduleMonth')->andReturn(Scheduler::NONE);
        $this->scheduler->shouldReceive('getScheduleWeek')->andReturn(0);

        $cronSchedule = 'cronSchedule';
        $this->scheduler->shouldReceive('getCronSchedule')->andReturn($cronSchedule);
        App::shouldReceive('make')->with('Cron\CronExpression', [$cronSchedule])->once()->andReturn($this->cronExpression);

        $returnValue = true;
        $this->cronExpression->shouldReceive('isDue')->once()->andReturn($returnValue);

        $this->assertEquals($returnValue, $this->interpreter->isDue());
    }

    /** ---- Week tests ---- */

    public function testThisWeekDefault()
    {
        $this->scheduler->shouldReceive('getScheduleMonth')->andReturn(Scheduler::NONE);
        $this->scheduler->shouldReceive('getScheduleWeek')->andReturn(false);
        $this->assertFalse($this->interpreter->thisWeek($this->scheduler));
    }

    public function testThisWeekAnyWeek()
    {
        $this->scheduler->shouldReceive('getScheduleWeek')->andReturn(Scheduler::ANY);
        $this->assertTrue($this->interpreter->thisWeek($this->scheduler));
    }

    public function testThisWeekOfGivenMonth()
    {
        $this->carbon->shouldReceive('format')->with('j')->andReturn(8);
        $this->scheduler->shouldReceive('getScheduleMonth')->andReturn(Month::JANUARY);
        $this->scheduler->shouldReceive('getScheduleWeek')->andReturn(2);
        $this->assertTrue($this->interpreter->thisWeek($this->scheduler));
    }

    public function testThisWeekCurrentWeek()
    {
        $current = date('W');
        $this->carbon->shouldReceive('format')->andReturn($current);
        $this->scheduler->shouldReceive('getScheduleWeek')->andReturn($current);
        $this->assertTrue($this->interpreter->thisWeek($this->scheduler));
    }

    public function testThisWeekSeriesOfWeeks()
    {
        $current = date('W');
        $this->carbon->shouldReceive('format')->andReturn($current);
        $months = [1, 2, $current];
        $this->scheduler->shouldReceive('getScheduleWeek')->andReturn($months);
        $this->assertTrue($this->interpreter->thisWeek($this->scheduler));
    }

    public function testThisWeekEvenWeek()
    {
        $this->carbon->shouldReceive('format')->with('j')->andReturn(2);
        $this->scheduler->shouldReceive('getScheduleWeek')->andReturn('even');
        $parser = new ScheduleInterpreter($this->scheduler, $this->carbon);

        $this->assertTrue($parser->thisWeek($this->scheduler));

        $this->carbon->shouldReceive('format')->with('W')->andReturn(1);
        $this->assertFalse($parser->thisWeek($this->scheduler));
    }

    public function testThisWeekOddWeek()
    {
        $this->carbon->shouldReceive('format')->with('j')->andReturn(3);
        $this->scheduler->shouldReceive('getScheduleWeek')->andReturn('odd');
        $parser = new ScheduleInterpreter($this->scheduler, $this->carbon);

        $this->assertFalse($parser->thisWeek($this->scheduler));

        $this->carbon->shouldReceive('format')->with('W')->andReturn(1);
        $this->assertTrue($parser->thisWeek($this->scheduler));
    }
}
