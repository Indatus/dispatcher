<?php namespace Indatus\Dispatcher\Drivers\DateTime;

/**
 * This class is responsible for parsing the expression syntax
 * Dispatcher uses for scheduling.
 *
 * @copyright   2014 Indatus
 * @package Indatus\Dispatcher\Drivers\PHP
 */

use App;
use Carbon\Carbon;
use Cron\CronExpression;

class ScheduleInterpreter
{
    /** @var Carbon */
    protected $now;

    /** @var Scheduler */
    protected $scheduler;

    protected $month        = null;
    protected $week         = null;
    protected $dayOfMonth   = null;
    protected $dayOfWeek    = null;
    protected $hour         = null;
    protected $minute       = null;

    public function __construct(Scheduler $scheduler, Carbon $now)
    {
        $this->now = $now;
        $this->scheduler = $scheduler;
    }

    /**
     * Determine if the current schedule is due to be run
     *
     * @return bool
     */
    public function isDue()
    {
        $cron = App::make('Cron\CronExpression', [$this->scheduler->getCronSchedule()]);

        // if a week is defined, so some special weekly stuff
        if ($this->scheduler->getScheduleWeek() !== Scheduler::NONE) {
            return $this->thisWeek() && $cron->isDue();
        }

        //otherwise us only standard cron scheduling
        return $cron->isDue();
    }

    /**
     * Determine if the provided expression refers to this week
     *
     * @return bool
     */
    public function thisWeek()
    {
        $scheduleWeek = $this->scheduler->getScheduleWeek();
        $currentWeek = $this->now->format('W');

        //if a month is defined, week refers to the week of the month
        $scheduleMonth = $this->scheduler->getScheduleMonth();
        if (!is_null($scheduleMonth) && $scheduleMonth !== Scheduler::NONE) {
            return $this->isCurrent($scheduleWeek, $this->now->weekOfMonth);
        }

        //if it's an odd or even week
        if ($scheduleWeek == 'odd' && $currentWeek & 1) {
            return true;
        } elseif ($scheduleWeek == 'even' && !($currentWeek & 1)) {
            return true;
        }

        return $this->isCurrent($scheduleWeek, $this->now->weekOfYear);
    }

    /**
     * This method checks syntax for all scheduling components.
     *
     *  - If there's a direct match
     *  - If it's an ANY match
     *  - If the expression exists in the series
     *
     * @param string $expression The expression to compare
     * @param string $current    The current value to compare against
     *
     * @return bool
     */
    protected function isCurrent($expression, $current)
    {
        // if it's any
        if ($expression == Scheduler::ANY) {
            return true;
        }

        // if this is in a series
        if (is_array($expression)) {
            return in_array($current, $expression);
        }

        // if there's a direct match
        if ((string) $expression === (string) $current) {
            return true;
        }

        return false;
    }
}
