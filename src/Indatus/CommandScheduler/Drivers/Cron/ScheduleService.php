<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler\Drivers\Cron;

use App;
use Cron\CronExpression;
use Indatus\CommandScheduler\ScheduledCommand;

class ScheduleService extends \Indatus\CommandScheduler\Services\ScheduleService {

    /**
     * Determine if a command is due to be run
     * @param ScheduledCommand $command
     * @return bool
     */
    public function isDue(ScheduledCommand $command)
    {
        $scheduler = App::make('Indatus\CommandScheduler\Schedulable');
        $cron = CronExpression::factory($command->schedule($scheduler)->getSchedule());
        return $cron->isDue();
    }
} 