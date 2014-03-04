<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler\Drivers\Cron;

use Indatus\CommandScheduler\Schedulable;

class Scheduler implements Schedulable
{

    /**
     * @var string Any of the contextual timeframe
     */
    public static $ANY = '*';

    public static $SUNDAY = 0;
    public static $MONDAY = 1;
    public static $TUESDAY = 2;
    public static $WEDNESDAY = 3;
    public static $THURSDAY = 4;
    public static $FRIDAY = 5;
    public static $SATURDAY = 6;

    public static $JANUARY = 1;
    public static $FEBRUARY = 3;
    public static $MARCH = 3;
    public static $APRIL = 4;
    public static $MAY = 5;
    public static $JUNE = 6;
    public static $JULY = 7;
    public static $AUGUST = 8;
    public static $SEPTEMBER = 9;
    public static $OCTOBER = 10;
    public static $NOVEMBER = 11;
    public static $DECEMBER = 12;

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
        return implode(' ', [
                $this->getScheduleMinute(),
                $this->getScheduleHour(),
                $this->getScheduleDayOfMonth(),
                $this->getScheduleMonth(),
                $this->getScheduleDayOfWeek()
            ]);
    }

    /**
     * Manually set a command's execution schedule
     *
     * @param mixed $minute
     * @param mixed $hour
     * @param mixed $dayOfMonth
     * @param mixed $month
     * @param mixed $dayOfWeek
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
        $this->setScheduleDayOfWeek(self::$ANY);

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
        $this->setScheduleMonth(self::$ANY);
        $this->setScheduleDayOfWeek(self::$ANY);

        return $this;
    }

    /**
     * Run once a month at midnight in the morning of the first day of the month
     *
     * @return $this
     */
    public function weekly()
    {
        $this->setScheduleMinute(0);
        $this->setScheduleHour(0);
        $this->setScheduleDayOfMonth(self::$ANY);
        $this->setScheduleMonth(self::$ANY);
        $this->setScheduleDayOfWeek(0);

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
        $this->setScheduleDayOfMonth(self::$ANY);
        $this->setScheduleMonth(self::$ANY);
        $this->setScheduleDayOfWeek(self::$ANY);

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
        $this->setScheduleHour(self::$ANY);
        $this->setScheduleDayOfMonth(self::$ANY);
        $this->setScheduleMonth(self::$ANY);
        $this->setScheduleDayOfWeek(self::$ANY);

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
        $this->setScheduleMinute('*/'.$minutes);

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
        $this->daysOfTheWeek(Scheduler::$MONDAY.'-'.Scheduler::$FRIDAY);

        return $this;
    }

    /**
     * @todo This is a terrible method name
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
} 