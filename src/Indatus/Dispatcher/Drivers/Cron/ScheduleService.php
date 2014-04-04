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
use Indatus\Dispatcher\Scheduling\ScheduledCommandInterface;
use Indatus\Dispatcher\Scheduling\ScheduleException;
use Indatus\Dispatcher\Scheduling\Schedulable;

class ScheduleService extends \Indatus\Dispatcher\Services\ScheduleService {

    /**
     * Determine if a command is due to be run
     *
     * @param \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface $command
     * @throws \Indatus\Dispatcher\Scheduling\ScheduleException
     * @return bool
     */
    public function isDue(ScheduledCommandInterface $command)
    {
        $scheduler = App::make('Indatus\Dispatcher\Schedulable');
        $schedules = $command->schedule($scheduler);
        if (!is_array($schedules)) {
            $schedules = array($schedules);
        }
        foreach ($schedules as $schedule) {
            if (($schedule instanceOf Schedulable) === false) {
                throw new ScheduleException('Schedule for "'.$command->getName().'" is not an instance of Schedulable');
            }

            $cron = CronExpression::factory($schedule->getSchedule());
            if ($cron->isDue()) {
                return true;
            }
        }

        return false;
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
            $scheduler = $command->schedule(App::make('Indatus\Dispatcher\Schedulable'));

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