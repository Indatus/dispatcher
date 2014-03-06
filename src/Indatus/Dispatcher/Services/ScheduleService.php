<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\Dispatcher\Services;

use App;
use Artisan;
use Indatus\Dispatcher\ScheduledCommand;
use Indatus\Dispatcher\Table;

abstract class ScheduleService
{

    /** @var  \Indatus\Dispatcher\Table */
    protected $table;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Determine if a command is due to be run
     * @param ScheduledCommand $command
     * @return bool
     */
    abstract public function isDue(ScheduledCommand $command);

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
     * @return \Indatus\Dispatcher\ScheduledCommand[]
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
     * Review scheduled commands schedule, active status, etc.
     * @todo refactor this... it's ugly.  The output goes directly to STDOUT
     * @return void
     */
    public function printSummary()
    {
        $this->table->setHeaders(['Environment(s)', 'Name', 'Minute', 'Hour', 'Day of Month', 'Month', 'Day of Week', 'Run as']);
        /** @var $command \Indatus\Dispatcher\ScheduledCommand */
        $commands = 0;
        $activeCommands = 0;
        foreach ($this->getScheduledCommands() as $command) {
            /** @var $command \Indatus\Dispatcher\ScheduledCommand */
            $scheduler = $command->schedule(App::make('Indatus\Dispatcher\Schedulable'));

            $this->table->addRow([
                    is_array($command->environment()) ? implode(',', $command->environment()) : $command->environment(),
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