<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */
namespace Indatus\Dispatcher;

interface Schedulable
{
    /**
     * Run every weekday
     * @return $this
     */
    public function everyWeekday();

    /**
     * Run once an hour at the beginning of the hour
     *
     * @return $this
     */
    public function hourly();

    /**
     * Set the days of the week under which this command will run
     * @param mixed $daysOfTheWeek
     * @return $this
     */
    public function daysOfTheWeek($daysOfTheWeek);

    /**
     * Set the months under which this command will run
     * @param mixed $monthsIntoTheYear
     * @return $this
     */
    public function months($monthsIntoTheYear);

    /**
     * Set the days of the month under which this command will run
     * @param mixed $hoursIntoTheDay
     * @return $this
     */
    public function daysOfTheMonth($hoursIntoTheDay);

    /**
     * Manually set a command's execution schedule
     *
     * @param mixed $minute
     * @param mixed $hour
     * @param mixed $dayOfMonth
     * @param mixed $month
     * @param mixed $dayOfWeek
     */
    public function setSchedule($minute, $hour, $dayOfMonth, $month, $dayOfWeek);

    /**
     * Run once a day at midnight
     *
     * @return $this
     */
    public function daily();

    /**
     * Run once a month at midnight in the morning of the first day of the month
     *
     * @return $this
     */
    public function monthly();

    /**
     * Set the hours under which this command will run
     * @param mixed $hoursIntoTheDay
     * @return $this
     */
    public function hours($hoursIntoTheDay);

    /**
     * Set the minutes under which this command will run
     * @param mixed $minutesIntoTheHour
     * @return $this
     */
    public function minutes($minutesIntoTheHour);

    /**
     * Run a command every X hours
     * @param $hours
     * @return $this
     */
    public function everyHours($hours);

    /**
     * Run once a month at midnight in the morning of the first day of the month
     *
     * @return $this
     */
    public function weekly();

    /**
     * Run a command every X months
     * @param $months
     * @return $this
     */
    public function everyMonths($months);

    /**
     * Run once a year at midnight in the morning of January 1
     *
     * @return $this
     */
    public function yearly();

    /**
     * Get the scheduling definition for the crontab
     *
     * @return string
     */
    public function getSchedule();

    /**
     * Run a command every X minutes
     * @param $minutes
     * @return $this
     */
    public function everyMinutes($minutes);

    /**
     * @return string
     */
    public function getScheduleDayOfMonth();

    /**
     * @return string
     */
    public function getScheduleDayOfWeek();

    /**
     * @return string
     */
    public function getScheduleHour();

    /**
     * @return string
     */
    public function getScheduleMinute();

    /**
     * @return string
     */
    public function getScheduleMonth();
}