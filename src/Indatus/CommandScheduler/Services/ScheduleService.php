<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler\Services;

use \Artisan;
use cli\Table;
use Indatus\CommandScheduler\ScheduledCommandInterface;
use Indatus\CommandScheduler\TableInterface;

class ScheduleService implements ScheduleServiceInterface
{

    /** @var  \Indatus\CommandScheduler\Table */
    private $table;

    public function __construct(TableInterface $table)
    {
        $this->table = $table;
    }

    /**
     * Get all commands that are scheduled
     * @todo test
     * @return array
     */
    public function getScheduledCommands()
    {
        $scheduledCommands = [];
        foreach (Artisan::all() as $command) {
            if ($command instanceOf ScheduledCommandInterface) {
                $scheduledCommands[] = $command;
            }
        }

        return $scheduledCommands;
    }

    /**
     * Review scheduled commands schedule, active status, etc.
     * @todo refactor this... it feels ugly
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