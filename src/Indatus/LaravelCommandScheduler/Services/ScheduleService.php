<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\LaravelCommandScheduler\Services;

use \Artisan;
use cli\Table;
use Indatus\LaravelCommandScheduler\ScheduledCommandInterface;

class ScheduleService {

    /** @var  \cli\Table */
    private $table;

    public function __construct(Table $table)
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

        //look at all the registered commands
        foreach (Artisan::all() as $command) {
            //see which ones are scheduled
            if ($command instanceOf ScheduledCommandInterface) {
                $scheduledCommands[] = $command;
            }
        }

        return $scheduledCommands;
    }

    /**
     * Review scheduled commands schedule, active status, etc.
     * @todo test
     * @return string
     */
    public function getSummary()
    {
        $this->table->setHeaders(['Active', 'Name', 'Minute', 'Hour', 'Day of Month', 'Month', 'Day of Week', 'Run as']);
        /** @var $command \Indatus\LaravelCommandScheduler\ScheduledCommand */
        foreach ($this->getScheduledCommands() as $command) {

            $scheduler = $command->getScheduler();

            $this->table->addRow([
                    ($command->isEnabled() ?  'Y' : 'N'),
                    $command->getName(),
                    $scheduler->getScheduleMinute(),
                    $scheduler->getScheduleHour(),
                    $scheduler->getScheduleDayOfMonth(),
                    $scheduler->getScheduleMonth(),
                    $scheduler->getScheduleDayOfWeek(),
                    $command->user()
                ]);
        }

        //sort by first column
        $this->table->sort(0);

        $this->table->display();
    }

}