<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler\Services;

use \App;
use \Artisan;
use \Cron\CronExpression;
use Indatus\CommandScheduler\ScheduledCommand;
use Indatus\CommandScheduler\Table;

class ScheduleService
{

    /** @var  \Indatus\CommandScheduler\Table */
    private $table;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Get all commands that are scheduled
     *
     * @return array
     */
    public function getScheduledCommands()
    {
        $scheduledCommands = [];
        foreach (Artisan::all() as $command) {
            if ($command instanceOf ScheduledCommand) {
                $scheduledCommands[] = $command;
            }
        }

        return $scheduledCommands;
    }

    /**
     * Get all commands that are due to be run
     *
     * @return \Indatus\CommandScheduler\ScheduledCommand[]
     */
    public function getDueCommands()
    {
        $commands = [];
        foreach ($this->getScheduledCommands() as $command) {
            if ($this->isDue($command)) {
                $commands[] = $command;
            }
        }
        return $commands;
    }

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

    /**
     * Review scheduled commands schedule, active status, etc.
     * @todo refactor this... it's ugly.  The output goes directly to STDOUT
     * @return void
     */
    public function printSummary()
    {
        $this->table->setHeaders(['Active', 'Environment(s)', 'Name', 'Minute', 'Hour', 'Day of Month', 'Month', 'Day of Week', 'Run as']);
        /** @var $command \Indatus\CommandScheduler\ScheduledCommand */
        $commands = 0;
        $activeCommands = 0;
        foreach ($this->getScheduledCommands() as $command) {

            $scheduler = $command->getScheduler();

            $this->table->addRow([
                    ($command->isEnabled() ?  'Y' : 'N'),
                    $command->getName(),
                    $command->getName(),
                    $scheduler->getScheduleMinute(),
                    $scheduler->getScheduleHour(),
                    $scheduler->getScheduleDayOfMonth(),
                    $scheduler->getScheduleMonth(),
                    $scheduler->getScheduleDayOfWeek(),
                    $command->user()
                ]);
            $commands++;
            $activeCommands++;
        }

        //sort by first column
        $this->table->sort(0);

        $this->table->display();

        \cli\line($activeCommands.' active of '.$commands.' scheduled commands');
    }

}