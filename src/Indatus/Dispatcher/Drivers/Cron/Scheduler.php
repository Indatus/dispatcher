<?php namespace Indatus\Dispatcher\Drivers\Cron;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App;
use Indatus\Dispatcher\Scheduling\Schedulable;

class Scheduler extends Schedulable
{

    /**
     * @var string Any of the contextual time frame
     */
    const ANY = '*';

    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    const JANUARY = 1;
    const FEBRUARY = 2;
    const MARCH = 3;
    const APRIL = 4;
    const MAY = 5;
    const JUNE = 6;
    const JULY = 7;
    const AUGUST = 8;
    const SEPTEMBER = 9;
    const OCTOBER = 10;
    const NOVEMBER = 11;
    const DECEMBER = 12;

    private $scheduleDayOfWeek = '*';
    private $scheduleMonth = '*';
    private $scheduleDayOfMonth = '*';
    private $scheduleHour = '*';
    private $scheduleMinute = '*';

    /**
     * Get the scheduling definition for the crontab
     *
     * @return string
     */
    public function getSchedule()
    {
        return implode(' ', array(
                $this->getScheduleMinute(),
                $this->getScheduleHour(),
                $this->getScheduleDayOfMonth(),
                $this->getScheduleMonth(),
                $this->getScheduleDayOfWeek()
            ));
    }

    /**
     * Manually set a command's execution schedule
     *
     * @param mixed $minute
     * @param mixed $hour
     * @param mixed $dayOfMonth
     * @param mixed $month
     * @param mixed $dayOfWeek
     * @return $this
     */
    public function setSchedule($minute, $hour, $dayOfMonth, $month, $dayOfWeek)
    {
        $minute = $this->parseTimeParameter($minute);
        $hour = $this->parseTimeParameter($hour);
        $dayOfMonth = $this->parseTimeParameter($dayOfMonth);
        $month = $this->parseTimeParameter($month);
        $dayOfWeek = $this->parseTimeParameter($dayOfWeek);

        $this->setScheduleMinute($minute);
        $this->setScheduleHour($hour);
        $this->setScheduleDayOfMonth($dayOfMonth);
        $this->setScheduleMonth($month);
        $this->setScheduleDayOfWeek($dayOfWeek);

        return $this;
    }

    /**
     * Run once a year at midnight in the morning of January 1
     *
     * @return $this
     */
    public function yearly()
    {
        $this->setScheduleMinute(0);
        $this->setScheduleHour(0);
        $this->setScheduleDayOfMonth(1);
        $this->setScheduleMonth(1);
        $this->setScheduleDayOfWeek(self::ANY);

        return $this;
    }

    /**
     * Run once a month at midnight in the morning of the first day of the month
     *
     * @return $this
     */
    public function monthly()
    {
        $this->setScheduleMinute(0);
        $this->setScheduleHour(0);
        $this->setScheduleDayOfMonth(1);
        $this->setScheduleMonth(self::ANY);
        $this->setScheduleDayOfWeek(self::ANY);

        return $this;
    }

    /**
     * Run once every other week at midnight on Sunday morning
     *
     * @return $this
     */
    public function everyOtherWeek()
    {
        return $this->weekly(2);
    }

    /**
     * Run once a week at midnight on Sunday morning
     *
     * @return $this
     */
    public function weekly($weeks=1)
    {
        /** @var \Carbon\Carbon $carbon */
        $carbon = App::make('Carbon\Carbon');

        if ($carbon->now()->weekOfYear % $weeks == 0) {
            $this->setScheduleMinute(0);
            $this->setScheduleHour(0);
            $this->setScheduleDayOfMonth(self::ANY);
            $this->setScheduleMonth(self::ANY);
            $this->setScheduleDayOfWeek(0);
        } else {
            return $this->never();
        }

        return $this;
    }

    /**
     * Run once a day at midnight
     *
     * @return $this
     */
    public function daily()
    {
        $this->setScheduleMinute(0);
        $this->setScheduleHour(0);
        $this->setScheduleDayOfMonth(self::ANY);
        $this->setScheduleMonth(self::ANY);
        $this->setScheduleDayOfWeek(self::ANY);

        return $this;
    }

    /**
     * Run once an hour at the beginning of the hour
     *
     * @return $this
     */
    public function hourly()
    {
        $this->setScheduleMinute(0);
        $this->setScheduleHour(self::ANY);
        $this->setScheduleDayOfMonth(self::ANY);
        $this->setScheduleMonth(self::ANY);
        $this->setScheduleDayOfWeek(self::ANY);

        return $this;
    }

    /**
     * Valid cron syntax that will never run. Feb 31st?!
     * @see http://stackoverflow.com/a/13938099
     * @return $this
     */
    public function never()
    {
        $this->setScheduleMinute(0);
        $this->setScheduleHour(0);
        $this->setScheduleDayOfMonth(31);
        $this->setScheduleMonth(2);
        $this->setScheduleDayOfWeek(self::ANY);

        return $this;
    }

    /**
     * Set the minutes under which this command will run
     * @param mixed $minutesIntoTheHour
     * @return $this
     */
    public function minutes($minutesIntoTheHour)
    {
        $this->setScheduleMinute($this->parseTimeParameter($minutesIntoTheHour));

        return $this;
    }

    /**
     * Run a command every X minutes
     * @param $minutes
     * @return $this
     */
    public function everyMinutes($minutes)
    {
        $minutesSchedule = self::ANY;
        if ($minutes != 1) {
            $minutesSchedule .= '/'.$minutes;
        }

        $this->setScheduleMinute($minutesSchedule);

        return $this;
    }

    /**
     * Set the hours under which this command will run
     * @param mixed $hoursIntoTheDay
     * @return $this
     */
    public function hours($hoursIntoTheDay)
    {
        $this->setScheduleHour($this->parseTimeParameter($hoursIntoTheDay));

        return $this;
    }

    /**
     * Run a command every X hours
     * @param $hours
     * @return $this
     */
    public function everyHours($hours)
    {
        $this->setScheduleHour('*/'.$hours);

        return $this;
    }

    /**
     * Set the days of the month under which this command will run
     * @param mixed $hoursIntoTheDay
     * @return $this
     */
    public function daysOfTheMonth($hoursIntoTheDay)
    {
        $this->setScheduleDayOfMonth($this->parseTimeParameter($hoursIntoTheDay));

        return $this;
    }

    /**
     * Set the months under which this command will run
     * @param mixed $monthsIntoTheYear
     * @return $this
     */
    public function months($monthsIntoTheYear)
    {
        $this->setScheduleMonth($this->parseTimeParameter($monthsIntoTheYear));

        return $this;
    }

    /**
     * Run a command every X months
     * @param $months
     * @return $this
     */
    public function everyMonths($months)
    {
        $this->setScheduleMonth('*/'.$months);

        return $this;
    }

    /**
     * Set the days of the week under which this command will run
     * @param mixed $daysOfTheWeek
     * @return $this
     */
    public function daysOfTheWeek($daysOfTheWeek)
    {
        $this->setScheduleDayOfWeek($this->parseTimeParameter($daysOfTheWeek));

        return $this;
    }

    /**
     * Run every weekday
     * @return $this
     */
    public function everyWeekday()
    {
        $this->daysOfTheWeek(self::MONDAY.'-'.self::FRIDAY);

        return $this;
    }

    /**
     * @inheritDoc
     * @return $this
     */
    public function args(array $arguments)
    {
        return parent::args($arguments);
    }

    /**
     * @inheritDoc
     * @return $this
     */
    public function opts(array $options)
    {
        return parent::opts($options);
    }

    /**
     * If an array of values is used, convert it
     * to a comma separated value.
     */
    private function parseTimeParameter($parameter)
    {
        if (is_array($parameter)) {
            return implode(',', $parameter);
        }

        return $parameter;
    }

    /**
     * @param string $scheduleDayOfMonth
     */
    private function setScheduleDayOfMonth($scheduleDayOfMonth)
    {
        $this->scheduleDayOfMonth = $scheduleDayOfMonth;
    }

    /**
     * @return string
     */
    public function getScheduleDayOfMonth()
    {
        return $this->scheduleDayOfMonth;
    }

    /**
     * @param string $scheduleDayOfWeek
     */
    private function setScheduleDayOfWeek($scheduleDayOfWeek)
    {
        $this->scheduleDayOfWeek = $scheduleDayOfWeek;
    }

    /**
     * @return string
     */
    public function getScheduleDayOfWeek()
    {
        return $this->scheduleDayOfWeek;
    }

    /**
     * @param string $scheduleHour
     */
    private function setScheduleHour($scheduleHour)
    {
        $this->scheduleHour = $scheduleHour;
    }

    /**
     * @return string
     */
    public function getScheduleHour()
    {
        return $this->scheduleHour;
    }

    /**
     * @param string $scheduleMinute
     */
    private function setScheduleMinute($scheduleMinute)
    {
        $this->scheduleMinute = $scheduleMinute;
    }

    /**
     * @return string
     */
    public function getScheduleMinute()
    {
        return $this->scheduleMinute;
    }

    /**
     * @param string $scheduleMonth
     */
    private function setScheduleMonth($scheduleMonth)
    {
        $this->scheduleMonth = $scheduleMonth;
    }

    /**
     * @return string
     */
    public function getScheduleMonth()
    {
        return $this->scheduleMonth;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSchedule();
    }
} 
