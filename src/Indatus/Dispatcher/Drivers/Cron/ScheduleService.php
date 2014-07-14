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
use Indatus\Dispatcher\Scheduling\ScheduledCommandInterface;
use Log;

class ScheduleService extends \Indatus\Dispatcher\Services\ScheduleService {

    /** @var \Indatus\Dispatcher\Table */
    protected $table;

    /**
     * Determine if a schedule is due to be run.
     *
     * @param \Indatus\Dispatcher\Scheduling\Schedulable $scheduler
     *
     * @return bool
     */
    public function isDue(Schedulable $scheduler)
    {
        try {
            $cron = CronExpression::factory($scheduler->getSchedule());
            return $cron->isDue();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function printSummary()
    {
        $this->table = App::make('Indatus\Dispatcher\Table');

        $this->table->setHeaders(array('Environment(s)', 'Name', 'Args/Opts', 'Minute', 'Hour', 'Day of Month', 'Month', 'Day of Week', 'Run as'));
        /** @var \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface $command */
        foreach ($this->getScheduledCommands() as $command) {

            /** @var \Indatus\Dispatcher\Scheduling\Schedulable $scheduler */
            $scheduler = $command->schedule(App::make('Indatus\Dispatcher\Scheduling\Schedulable'));

            //if there's only one schedule, print just the command
            if (!is_array($scheduler)) {
                $this->printCommand($command, $scheduler);
            } else {

                if ($this->printCommandLabel($command)) {
                    /** @var \Indatus\Dispatcher\Scheduling\Schedulable $schedule */
                    foreach ($scheduler as $schedule) {
                        $this->printSchedule($command, $schedule);
                    }
                }
            }
        }

        $this->table->display();
    }

    protected function printCommand(ScheduledCommandInterface $command, Schedulable $scheduler)
    {
        $this->table->addRow(array(
                is_array($command->environment()) ? implode(',', $command->environment()) : $command->environment(),
                $command->getName(),
                '',
                $scheduler->getScheduleMinute(),
                $scheduler->getScheduleHour(),
                $scheduler->getScheduleDayOfMonth(),
                $scheduler->getScheduleMonth(),
                $scheduler->getScheduleDayOfWeek(),
                $command->user()
            ));
    }

    protected function printCommandLabel(ScheduledCommandInterface $command)
    {
        $this->table->addRow(array(
                is_array($command->environment()) ? implode(',', $command->environment()) : $command->environment(),
                $command->getName(),
                '',
                '',
                '',
                '',
                '',
                '',
                $command->user()
            ));
        return true;
    }

    protected function printSchedule(ScheduledCommandInterface $command, Schedulable $scheduler)
    {
        $commandService = App::make('Indatus\Dispatcher\Services\CommandService');

        $this->table->addRow(array(
                '',
                '',
                trim($commandService->prepareArguments($scheduler->getArguments()).' '.$commandService->prepareOptions($scheduler->getOptions())),
                $scheduler->getScheduleMinute(),
                $scheduler->getScheduleHour(),
                $scheduler->getScheduleDayOfMonth(),
                $scheduler->getScheduleMonth(),
                $scheduler->getScheduleDayOfWeek(),
                ''
            ));
    }
} 