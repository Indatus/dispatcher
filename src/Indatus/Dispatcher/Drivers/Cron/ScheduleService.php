<?php

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Indatus\Dispatcher\Drivers\Cron;

use App;
use Cron\CronExpression;
use Indatus\Dispatcher\ScheduledCommand;

class ScheduleService extends \Indatus\Dispatcher\Services\ScheduleService {

    /**
     * Determine if a command is due to be run
     * @param ScheduledCommand $command
     * @return bool
     */
    public function isDue(ScheduledCommand $command)
    {
        $scheduler = App::make('Indatus\Dispatcher\Schedulable');
        $cron = CronExpression::factory($command->schedule($scheduler)->getSchedule());
        return $cron->isDue();
    }

    /**
     * @inheritDoc
     */
    public function printSummary()
    {
        $this->table->setHeaders(array('Environment(s)', 'Name', 'Minute', 'Hour', 'Day of Month', 'Month', 'Day of Week', 'Run as'));
        /** @var $command \Indatus\Dispatcher\ScheduledCommand */
        $commands = 0;
        $activeCommands = 0;
        foreach ($this->getScheduledCommands() as $command) {
            /** @var $command \Indatus\Dispatcher\ScheduledCommand */
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
            $commands++;
            $activeCommands++;
        }

        //sort by first column
        $this->table->sort(0);

        $this->table->display();
    }
} 