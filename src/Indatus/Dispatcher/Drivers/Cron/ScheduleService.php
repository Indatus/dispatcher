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
} 