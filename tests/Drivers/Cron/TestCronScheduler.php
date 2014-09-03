<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Mockery as m;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;

class TestCronScheduler extends TestCase
{
    /** @var Indatus\Dispatcher\Drivers\Cron\Scheduler */
    private $scheduler;

    private $schedulerClass = 'Indatus\Dispatcher\Scheduling\Schedulable';

    /** @var string Should be: "* * * * *" */
    private $defaultSchedule;

    public function setUp()
    {
        parent::setUp();
        $this->scheduler = new Scheduler(App::make('Indatus\Dispatcher\ConfigResolver'));

        $this->defaultSchedule = implode(' ', array_fill(0, 5, Scheduler::ANY));
    }

    /**
     * Test default schedule values and getSchedule() building
     */
    public function testBuildingSchedule()
    {
        $this->assertEquals($this->scheduler->getSchedule(), $this->defaultSchedule);
        $this->assertEquals($this->scheduler.'', $this->defaultSchedule);
    }

    public function testSetSchedule()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->setSchedule(1, 2, 3, 4, 5));
        $this->assertEquals($this->scheduler->getSchedule(), '1 2 3 4 5');
    }

    public function testYearly()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->yearly());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 1 1 '.Scheduler::ANY);
    }

    public function testMonthly()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->monthly());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 1 '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testEveryOtherWeekEven()
    {
        $carbon = m::mock();
        $carbon->shouldReceive('now')->andReturn($carbon);
        $carbon->weekOfYear = 32;
        App::instance('Carbon\Carbon', $carbon);

        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyOtherWeek());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 '.Scheduler::ANY.' '.Scheduler::ANY.' 0');
    }

    public function testEveryOtherWeekOdd()
    {
        $carbon = m::mock();
        $carbon->shouldReceive('now')->andReturn($carbon);
        $carbon->weekOfYear = 33;
        App::instance('Carbon\Carbon', $carbon);

        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyOtherWeek());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 31 2 '.Scheduler::ANY);
    }

    public function testWeekly()
    {
        $carbon = m::mock();
        $carbon->shouldReceive('now')->andReturn($carbon);
        App::instance('Carbon\Carbon', $carbon);

        $carbon->weekOfYear = 33;

        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->weekly());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 '.Scheduler::ANY.' '.Scheduler::ANY.' 0');

        $carbon->weekOfYear = 34;

        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->weekly());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 '.Scheduler::ANY.' '.Scheduler::ANY.' 0');
    }

    public function testDaily()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->daily());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testHourly()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->hourly());
        $this->assertEquals($this->scheduler->getSchedule(), '0 '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testNever()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->never());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 31 2 '.Scheduler::ANY);
    }

    public function testMinutes()
    {
        $minutes = 15;
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->minutes($minutes));
        $this->assertEquals($this->scheduler->getSchedule(), $minutes.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);

        //test that we can specify arrays of times
        $this->scheduler->minutes(array($minutes, $minutes+1));
        $this->assertEquals($this->scheduler->getSchedule(), $minutes.','.($minutes+1).' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testEveryMinutes()
    {
        $minutes = 30;
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyMinutes($minutes));

        $this->assertEquals($this->scheduler->getSchedule(), '*/'.$minutes.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testEverySingleMinute()
    {
        $minutes = 1;
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyMinutes($minutes));

        $this->assertEquals($this->scheduler->getSchedule(), '* '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testHours()
    {
        $hours = 15;
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->hours($hours));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.$hours.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);

        //test that we can specify arrays of times
        $this->scheduler->hours(array($hours, $hours+1));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.$hours.','.($hours+1).' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testEveryHours()
    {
        $hours = 6;
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyHours($hours));

        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' */'.$hours.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testDaysOfTheMonth()
    {
        $daysOfTheMonth = 14;
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->daysOfTheMonth($daysOfTheMonth));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.$daysOfTheMonth.' '.Scheduler::ANY.' '.Scheduler::ANY);

        //test that we can specify arrays of times
        $this->scheduler->daysOfTheMonth(array($daysOfTheMonth, $daysOfTheMonth+1));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.$daysOfTheMonth.','.($daysOfTheMonth+1).' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testMonths()
    {
        $months = 4;
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->months($months));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.$months.' '.Scheduler::ANY);

        //test that we can specify arrays of times
        $this->scheduler->months(array($months, $months+1));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.$months.','.($months+1).' '.Scheduler::ANY);
    }

    public function testEveryMonths()
    {
        $months = 6;
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyMonths($months));

        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' */'.$months.' '.Scheduler::ANY);
    }

    public function testEveryWeekday()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyWeekday());
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::MONDAY.'-'.Scheduler::FRIDAY);
    }

    public function testArgs()
    {
        $args = array('testArgument');

        /** @var \Indatus\Dispatcher\Drivers\Cron\Scheduler $scheduler */
        $scheduler = $this->scheduler->args($args);
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
    }


    public function testOpts()
    {
        $opts = array(
            'testOpt',
            'option' => 'value'
        );

        $expectedOpts = array(
            'testOpt',
            'option' => 'value',
            'env' => 'testing'
        );

        $args = array(
            'testArgument'
        );

        /** @var \Indatus\Dispatcher\Drivers\Cron\Scheduler $scheduler */
        $scheduler = $this->scheduler->args($args)->opts($opts)->everyWeekday();
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
        $this->assertEquals($expectedOpts, $scheduler->getOptions());
        $this->assertNotEquals($scheduler->getSchedule(), $this->defaultSchedule);

        /** @var \Indatus\Dispatcher\Drivers\Cron\Scheduler $scheduler */
        $scheduler = $this->scheduler->opts($opts)->args($args);
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
        $this->assertEquals($expectedOpts, $scheduler->getOptions());

        //be sure schedule reset, if not then we didn't get a fresh SchedulerClass
        $this->assertEquals($scheduler->getSchedule(), $this->defaultSchedule);
    }

    public function testOptsWithSpecificEnvironmentSet()
    {
        $opts = array(
            'testOpt',
            'option' => 'value',
            'env' => 'a_fancy_environment'
        );

        $expectedOpts = array(
            'testOpt',
            'option' => 'value',
            'env' => 'a_fancy_environment'
        );

        $args = array(
            'testArgument'
        );

        /** @var \Indatus\Dispatcher\Drivers\Cron\Scheduler $scheduler */
        $scheduler = $this->scheduler->args($args)->opts($opts)->everyWeekday();
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
        $this->assertEquals($expectedOpts, $scheduler->getOptions());
        $this->assertNotEquals($scheduler->getSchedule(), $this->defaultSchedule);

        /** @var \Indatus\Dispatcher\Drivers\Cron\Scheduler $scheduler */
        $scheduler = $this->scheduler->opts($opts)->args($args);
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
        $this->assertEquals($expectedOpts, $scheduler->getOptions());

        //be sure schedule reset, if not then we didn't get a fresh SchedulerClass
        $this->assertEquals($scheduler->getSchedule(), $this->defaultSchedule);
    }

} 
