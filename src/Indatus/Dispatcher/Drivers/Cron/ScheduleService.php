<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
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
} 