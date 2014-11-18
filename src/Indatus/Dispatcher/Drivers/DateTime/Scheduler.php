<?php namespace Indatus\Dispatcher\Drivers\DateTime;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Indatus\Dispatcher\Day;
use Indatus\Dispatcher\Month;
use Indatus\Dispatcher\Scheduling\BadScheduleException;
use Indatus\Dispatcher\Scheduling\Schedulable;

class Scheduler extends Schedulable
{
    const ANY = '*';

    const NONE = '-';

    private $scheduleWeek = self::NONE;
    private $scheduleDayOfWeek = self::ANY;
    private $scheduleMonth = self::ANY;
    private $scheduleDayOfMonth = self::ANY;
    private $scheduleHour = self::ANY;
    private $scheduleMinute = self::ANY;

    /**
     * Get the scheduling definition in a readable way
     *
     * @return string
     */
    public function getSchedule()
    {
        return implode(' ', [
                $this->getScheduleMonth(),
                $this->getScheduleWeek(),
                $this->getScheduleDayOfMonth(),
                $this->getScheduleDayOfWeek(),
                $this->getScheduleHour(),
                $this->getScheduleMinute()
            ]);
    }

    /**
     * Get a valid cron schedule
     *
     * @return string
     */
    public function getCronSchedule()
    {
        $schedules = explode(' ', $this->getSchedule());

        //remove week
        unset($schedules[1]);

        return implode(' ', $schedules);
    }

    /**
     * Manually set a command's execution schedule.  Parameter
     * order follows standard cron syntax
     *
     * @param int|array        $minute
     * @param int|array        $hour
     * @param int|array        $dayOfMonth
     * @param int|array        $month
     * @param int|array        $dayOfWeek
     * @param string|int|array $week
     *
     * @return $this
     */
    public function setSchedule($minute, $hour, $dayOfMonth, $month, $dayOfWeek, $week = self::NONE)
    {
        $month = $this->parseTimeParameter($month);
        $week = $this->parseTimeParameter($week);
        $dayOfMonth = $this->parseTimeParameter($dayOfMonth);
        $dayOfWeek = $this->parseTimeParameter($dayOfWeek);
        $hour = $this->parseTimeParameter($hour);
        $minute = $this->parseTimeParameter($minute);

        $this->setScheduleMonth($month);
        $this->setScheduleWeek($week);
        $this->setScheduleDayOfMonth($dayOfMonth);
        $this->setScheduleDayOfWeek($dayOfWeek);
        $this->setScheduleHour($hour);
        $this->setScheduleMinute($minute);

        return $this;
    }

    /**
     * Run once a year at midnight in the morning of January 1
     *
     * @return $this
     */
    public function yearly()
    {
        $this->setScheduleMonth(1);
        $this->setScheduleWeek(self::NONE);
        $this->setScheduleDayOfMonth(1);
        $this->setScheduleDayOfWeek(self::ANY);
        $this->setScheduleHour(0);
        $this->setScheduleMinute(0);

        return $this;
    }

    /**
     * Run once a quarter at the beginning of the quarter
     *
     * @return $this
     */
    public function quarterly()
    {
        $months = [Month::JANUARY, Month::APRIL, Month::JULY, Month::OCTOBER];
        $this->setScheduleMonth($this->parseTimeParameter($months));
        $this->setScheduleWeek(self::NONE);
        $this->setScheduleDayOfMonth(1);
        $this->setScheduleDayOfWeek(self::ANY);
        $this->setScheduleHour(0);
        $this->setScheduleMinute(0);

        return $this;
    }

    /**
     * Set the days of the month under which this command will run
     * @param  int|array $daysOfTheMonth
     * @return $this
     */
    public function daysOfTheMonth($daysOfTheMonth)
    {
        $this->setScheduleDayOfMonth($this->parseTimeParameter($daysOfTheMonth));

        return $this;
    }

    /**
     * Run once a month at midnight in the morning of the first day of the month
     *
     * @return $this
     */
    public function monthly()
    {
        $this->setScheduleMonth(self::ANY);
        $this->setScheduleWeek(self::NONE);
        $this->setScheduleDayOfMonth(1);
        $this->setScheduleDayOfWeek(self::ANY);
        $this->setScheduleHour(0);
        $this->setScheduleMinute(0);

        return $this;
    }

    /**
     * Set the months under which this command will run
     * @param  int|array $monthsIntoTheYear
     * @return $this
     */
    public function months($monthsIntoTheYear)
    {
        $this->setScheduleMonth($this->parseTimeParameter($monthsIntoTheYear));

        return $this;
    }

    /**
     * Run a command every X months
     * @param  int   $months
     * @return $this
     */
    public function everyMonths($months)
    {
        $this->setScheduleMonth('*/'.$months);

        return $this;
    }

    /**
     * Run once every odd week at midnight on Sunday morning
     *
     * @return $this
     */
    public function everyOddWeek()
    {
        $this->setScheduleMonth(self::ANY);
        $this->setScheduleWeek('odd');
        $this->setScheduleDayOfMonth(self::ANY);
        $this->setScheduleDayOfWeek(self::ANY);
        $this->setScheduleHour(0);
        $this->setScheduleMinute(0);

        return $this;
    }

    /**
     * Run once every even week at midnight on Sunday morning
     *
     * @return $this
     */
    public function everyEvenWeek()
    {
        $this->setScheduleMonth(self::ANY);
        $this->setScheduleWeek('even');
        $this->setScheduleDayOfMonth(self::ANY);
        $this->setScheduleDayOfWeek(self::ANY);
        $this->setScheduleHour(0);
        $this->setScheduleMinute(0);

        return $this;
    }

    /**
     * Run once a week at midnight on Sunday morning
     *
     * @return $this
     */
    public function weekly()
    {
        $this->setScheduleMonth(self::ANY);
        $this->setScheduleWeek(self::ANY);
        $this->setScheduleDayOfMonth(self::ANY);
        $this->setScheduleDayOfWeek(Day::SUNDAY);
        $this->setScheduleHour(0);
        $this->setScheduleMinute(0);

        return $this;
    }

    /**
     * Run on the given week of each month
     *
     * @param int|array $week
     *
     * @throws BadScheduleException
     *
     * @return $this
     */
    public function week($week)
    {
        $this->setScheduleWeek($this->parseTimeParameter($week));

        return $this;
    }

    /**
     * Run on the given week of each month
     *
     * @param int|array $week
     *
     * @throws BadScheduleException
     * @return $this
     */
    public function weekOfYear($week)
    {
        $this->setScheduleMonth(self::NONE);
        $this->setScheduleWeek($this->parseTimeParameter($week));
        $this->setScheduleDayOfMonth(self::ANY);
        $this->setScheduleDayOfWeek(Day::SUNDAY);
        $this->setScheduleHour(0);
        $this->setScheduleMinute(0);

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
     * Set the days of the week under which this command will run
     * @param  int|array $daysOfTheWeek
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
        $this->daysOfTheWeek(Day::MONDAY.'-'.Day::FRIDAY);

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
     * Set the hours under which this command will run
     * @param  int|array $hoursIntoTheDay
     * @return $this
     */
    public function hours($hoursIntoTheDay)
    {
        $this->setScheduleHour($this->parseTimeParameter($hoursIntoTheDay));

        return $this;
    }

    /**
     * Run a command every X hours
     * @param  int   $hours
     * @return $this
     */
    public function everyHours($hours)
    {
        $this->setScheduleHour('*/'.$hours);

        return $this;
    }

    /**
     * Set the minutes under which this command will run
     * @param  int|array $minutesIntoTheHour
     * @return $this
     */
    public function minutes($minutesIntoTheHour)
    {
        $this->setScheduleMinute($this->parseTimeParameter($minutesIntoTheHour));

        return $this;
    }

    /**
     * Run a command every X minutes
     * @param  int   $minutes
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
     * @inheritDoc
     * @return Scheduler
     */
    public function args(array $arguments)
    {
        return parent::args($arguments);
    }

    /**
     * @inheritDoc
     * @return Scheduler
     */
    public function opts(array $options)
    {
        return parent::opts($options);
    }

    /**
     * If an array of values is used, convert it
     * to a comma separated value.
     */
    protected function parseTimeParameter($parameter)
    {
        if (is_array($parameter)) {
            return implode(',', $parameter);
        }

        return $parameter;
    }

    /**
     * @param string $scheduleWeek
     */
    protected function setScheduleWeek($scheduleWeek)
    {
        $this->scheduleWeek = $scheduleWeek;
    }

    /**
     * @return string
     */
    public function getScheduleWeek()
    {
        return $this->scheduleWeek;
    }

    /**
     * @param string $scheduleDayOfMonth
     */
    protected function setScheduleDayOfMonth($scheduleDayOfMonth)
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
    protected function setScheduleDayOfWeek($scheduleDayOfWeek)
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
    protected function setScheduleHour($scheduleHour)
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
    protected function setScheduleMinute($scheduleMinute)
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
    protected function setScheduleMonth($scheduleMonth)
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
