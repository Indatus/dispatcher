<?php namespace Indatus\Dispatcher\Drivers\DateTime;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use App;
use Indatus\Dispatcher\Day;
use Indatus\Dispatcher\Month;
use TestCase;

class TestScheduler extends TestCase
{
    /** @var \Indatus\Dispatcher\Drivers\DateTime\Scheduler */
    private $scheduler;

    private $schedulerClass = 'Indatus\Dispatcher\Scheduling\Schedulable';

    /** @var string Should be: "* - * * * *" */
    private $defaultSchedule;

    public function setUp()
    {
        parent::setUp();

        $this->carbon = App::make('Carbon\Carbon');

        $this->scheduler = App::make('Indatus\Dispatcher\Drivers\DateTime\Scheduler');

        $pieces = array_fill(0, 6, Scheduler::ANY);
        $pieces[1] = Scheduler::NONE;
        $this->defaultSchedule = implode(' ', $pieces);
    }

    /**
     * Test default schedule values and getSchedule() building
     */
    public function testBuildingSchedule()
    {
        $this->assertEquals($this->scheduler->getSchedule(), $this->defaultSchedule);
        $this->assertEquals($this->scheduler.'', $this->defaultSchedule);

        //assert that summaries are printed with
        $array = [1,2];
        $this->scheduler->months($array);
        $this->scheduler->daysOfTheMonth($array);
        $this->scheduler->daysOfTheWeek($array);
        $this->scheduler->hours($array);
        $this->scheduler->minutes($array);

        $this->assertEquals($this->scheduler->getSchedule(), '1,2 '.Scheduler::NONE.' 1,2 1,2 1,2 1,2');
    }

    public function testSetSchedule()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->setSchedule(1, 2, 3, 4, 5, 6));
        $this->assertEquals($this->scheduler->getSchedule(), '4 6 3 5 2 1');
    }

    public function testGetCronSchedule()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->setSchedule(1, 2, 3, 4, 5, 6));
        $this->assertEquals($this->scheduler->getCronSchedule(), '4 3 5 2 1');
    }

    public function testYearly()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->yearly());
        $this->assertEquals($this->scheduler->getSchedule(), Month::JANUARY.' '.Scheduler::NONE.' 1 '.Scheduler::ANY.' 0 0');
    }

    public function testQuarterly()
    {
        $months = implode(',', [Month::JANUARY, Month::APRIL, Month::JULY, Month::OCTOBER]);
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->quarterly());
        $this->assertEquals($this->scheduler->getSchedule(), $months.' '.Scheduler::NONE.' 1 '.Scheduler::ANY.' 0 0');
    }

    public function testMonthly()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->monthly());
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::NONE.' 1 '.Scheduler::ANY.' 0 0');
    }

    public function testEveryOddWeek()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyOddWeek());
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' odd '.Scheduler::ANY.' '.Scheduler::ANY.' 0 0');
    }

    public function testEveryEvenWeek()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyEvenWeek());
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' even '.Scheduler::ANY.' '.Scheduler::ANY.' 0 0');
    }

    public function testWeekly()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->weekly());
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Day::SUNDAY.' 0 0');
    }

    public function testWeek()
    {
        $array = [2, 4];
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->week($array));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.implode(',', $array).' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testWeekOfYear()
    {
        $array = [2, 4];
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->weekOfYear($array));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::NONE.' '.implode(',', $array).' '.Scheduler::ANY.' 0 0 0');
    }

    public function testDaily()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->daily());
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::NONE.' '.Scheduler::ANY.' '.Scheduler::ANY.' 0 0');
    }

    public function testHourly()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->hourly());
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::NONE.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' 0');
    }

    public function testMinutes()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->minutes(30));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::NONE.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' 30');
    }

    public function testEveryMinutes()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyMinutes(30));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::NONE.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' */30');
    }

    public function testHours()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->hours(3));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::NONE.' '.Scheduler::ANY.' '.Scheduler::ANY.' 3 '.Scheduler::ANY);

        $array = [3,4,5];
        $this->scheduler->hours($array);
        $this->assertEquals(implode(',', $array), $this->scheduler->getScheduleHour());
    }

    public function testEveryHours()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyHours(3));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::NONE.' '.Scheduler::ANY.' '.Scheduler::ANY.' */3 '.Scheduler::ANY);
    }

    public function testDaysOfTheMonth()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->daysOfTheMonth(3));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::NONE.' 3 '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);

        $array = [3,4,5];
        $this->scheduler->daysOfTheMonth($array);
        $this->assertEquals(implode(',', $array), $this->scheduler->getScheduleDayOfMonth());
    }

    public function testMonths()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->months(3));
        $this->assertEquals($this->scheduler->getSchedule(), '3 '.Scheduler::NONE.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);

        $array = [3,4,5];
        $this->scheduler->months($array);
        $this->assertEquals(implode(',', $array), $this->scheduler->getScheduleMonth());
    }

    public function testEveryMonths()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyMonths(2));
        $this->assertEquals($this->scheduler->getSchedule(), '*/2 '.Scheduler::NONE.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY.' '.Scheduler::ANY);
    }

    public function testDaysOfTheWeek()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->daysOfTheWeek(Day::SUNDAY));
        $this->assertEquals($this->scheduler->getSchedule(), Scheduler::ANY.' '.Scheduler::NONE.' '.Scheduler::ANY.' '.Day::SUNDAY.' '.Scheduler::ANY.' '.Scheduler::ANY);

        $array = [Day::FRIDAY, Day::THURSDAY, Day::TUESDAY];
        $this->scheduler->daysOfTheWeek($array);
        $this->assertEquals(implode(',', $array), $this->scheduler->getScheduleDayOfWeek());
    }

    public function testEveryWeekday()
    {
        $this->assertInstanceOf($this->schedulerClass, $this->scheduler->everyWeekday());
        $this->assertEquals($this->scheduler->getScheduleDayOfWeek(), Day::MONDAY.'-'.Day::FRIDAY);
    }

    public function testArgs()
    {
        $args = ['testArgument'];

        /** @var \Indatus\Dispatcher\Drivers\DateTime\Scheduler $scheduler */
        $scheduler = $this->scheduler->args($args);
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
    }

    public function testOpts()
    {
        $opts = [
            'testOpt',
            'option' => 'value',
        ];
        $args = [

        ];

        $expectedOpts = [
            'testOpt',
            'option' => 'value',
            'env' => 'testing',
        ];

        $args = [
            'testArgument',
        ];

        /** @var \Indatus\Dispatcher\Drivers\DateTime\Scheduler $scheduler */
        $scheduler = $this->scheduler->args($args)->opts($opts)->everyWeekday();
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
        $this->assertEquals($expectedOpts, $scheduler->getOptions());
        $this->assertNotEquals($scheduler->getSchedule(), $this->defaultSchedule);

        /** @var \Indatus\Dispatcher\Drivers\DateTime\Scheduler $scheduler */
        $scheduler = $this->scheduler->opts($opts)->args($args);
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
        $this->assertEquals($expectedOpts, $scheduler->getOptions());

        //be sure schedule reset, if not then we didn't get a fresh SchedulerClass
        $this->assertEquals($scheduler->getSchedule(), $this->defaultSchedule);
    }

    public function testOptsWithSpecificEnvironmentSet()
    {
        $opts = [
            'testOpt',
            'option' => 'value',
            'env' => 'a_fancy_environment',
        ];

        $expectedOpts = [
            'testOpt',
            'option' => 'value',
            'env' => 'a_fancy_environment',
        ];

        $args = [
            'testArgument',
        ];

        /** @var \Indatus\Dispatcher\Drivers\DateTime\Scheduler $scheduler */
        $scheduler = $this->scheduler->args($args)->opts($opts)->everyWeekday();
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
        $this->assertEquals($expectedOpts, $scheduler->getOptions());
        $this->assertNotEquals($scheduler->getSchedule(), $this->defaultSchedule);

        /** @var \Indatus\Dispatcher\Drivers\DateTime\Scheduler $scheduler */
        $scheduler = $this->scheduler->opts($opts)->args($args);
        $this->assertInstanceOf($this->schedulerClass, $scheduler);
        $this->assertEquals($args, $scheduler->getArguments());
        $this->assertEquals($expectedOpts, $scheduler->getOptions());

        //be sure schedule reset, if not then we didn't get a fresh SchedulerClass
        $this->assertEquals($scheduler->getSchedule(), $this->defaultSchedule);
    }
}
