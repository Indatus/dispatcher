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
use Cron\CronExpression;
use Indatus\Dispatcher\Scheduling\Schedulable;

class ScheduleService extends \Indatus\Dispatcher\Services\ScheduleService {

    /**
     * Determine if a schedule is due to be run
     *
     * @param \Indatus\Dispatcher\Scheduling\Schedulable    $scheduler
     *
     * @return bool
     */
    public function isDue(Schedulable $scheduler)
    {
        $cron = CronExpression::factory($scheduler->getSchedule());
        return $cron->isDue();
    }

    /**
     * @inheritDoc
     */
    public function printSummary()
    {
        $this->table->setHeaders(array('Environment(s)', 'Name', 'Minute', 'Hour', 'Day of Month', 'Month', 'Day of Week', 'Run as'));
        /** @var $command \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface */
        foreach ($this->getScheduledCommands() as $command) {
            /** @var $command \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface */
            $scheduler = $command->schedule(App::make('Indatus\Dispatcher\Scheduling\Schedulable'));

            $this->table->addRow(array(
                    is_array($command->environment()) ? implode(',', $command->environment()) : $command->environment(),
                    $command->getName(),
                    $scheduler->getScheduleMinute(),
                    $scheduler->getScheduleHour(),
                    $scheduler->getScheduleDayOfMonth(),
                    $scheduler->getScheduleMonth(),
                    $scheduler->getScheduleDayOfWeek(),
                    $command->user()
                ));
        }

        //sort by first column
        $this->table->sort(0);

        $this->table->display();
    }
} 