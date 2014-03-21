<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use \Indatus\Dispatcher\Drivers\Cron\Scheduler;

class TestScheduler extends TestCase
{
    /**
     * @var Indatus\Dispatcher\Scheduler
     */
    private $scheduler;

    private $schedularClass = 'Indatus\Dispatcher\Schedulable';

    public function setUp()
    {
        $this->scheduler = new Scheduler();
    }

    /**
     * Test default schedule values and getSchedule() building
     */
    public function testBuildingSchedule()
    {
        $this->assertEquals($this->scheduler->getSchedule(), '* * * * *');
    }

    public function testSetSchedule()
    {
        $this->scheduler->setSchedule(1, 2, 3, 4, 5);
        $this->assertEquals($this->scheduler->getSchedule(), '1 2 3 4 5');
    }

    public function testYearly()
    {
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->yearly());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 1 1 '.Scheduler::ANY);
    }

    public function testMonthly()
    {
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->monthly());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 1 '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testWeekly()
    {
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->weekly());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 '.Scheduler::ANY.' '.Scheduler::ANY.' 0');
    }

    public function testDaily()
    {
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->daily());
        $this->assertEquals($this->scheduler->getSchedule(), '0 0 '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testHourly()
    {
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->hourly());
        $this->assertEquals($this->scheduler->getSchedule(), '0 '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testMinutes()
    {
        $minutes = 15;
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->minutes($minutes));
        $this->assertEquals($this->scheduler->getSchedule(), $minutes.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);

        //test that we can specify arrays of times
        $this->scheduler->minutes([$minutes, $minutes+1]);
        $this->assertEquals($this->scheduler->getSchedule(), $minutes.','.($minutes+1).' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testEveryMinutes()
    {
        $minutes = 30;
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->everyMinutes($minutes));

        $this->assertEquals($this->scheduler->getSchedule(), '*/'.$minutes.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testEverySingleMinute()
    {
        $minutes = 1;
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->everyMinutes($minutes));

        $this->assertEquals($this->scheduler->getSchedule(), '* '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testHours()
    {
        $hours = 15;
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->hours($hours));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.$hours.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);

        //test that we can specify arrays of times
        $this->scheduler->hours([$hours, $hours+1]);
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.$hours.','.($hours+1).' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testEveryHours()
    {
        $hours = 6;
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->everyHours($hours));

        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' */'.$hours.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testDaysOfTheMonth()
    {
        $daysOfTheMonth = 14;
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->daysOfTheMonth($daysOfTheMonth));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.$daysOfTheMonth.' '.Scheduler::ANY.' '.Scheduler::ANY);

        //test that we can specify arrays of times
        $this->scheduler->daysOfTheMonth([$daysOfTheMonth, $daysOfTheMonth+1]);
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.$daysOfTheMonth.','.($daysOfTheMonth+1).' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testMonths()
    {
        $months = 4;
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->months($months));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.$months.' '.Scheduler::ANY);

        //test that we can specify arrays of times
        $this->scheduler->months([$months, $months+1]);
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.$months.','.($months+1).' '.Scheduler::ANY);
    }

    public function testEveryMonths()
    {
        $months = 6;
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->everyMonths($months));

        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' */'.$months.' '.Scheduler::ANY);
    }

    public function testEveryWeekday()
    {
        $this->assertInstanceOf($this->schedularClass, $this->scheduler->everyWeekday());
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::MONDAY.'-'.Scheduler::FRIDAY);
    }

} 