<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler\Services;

use Indatus\CommandScheduler\ScheduledCommand;
use App;

class CommandService
{

    /**
     * @var /Indatus\CommandScheduler\Services\ScheduleService
     */
    private $scheduleService;

    function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Run all commands that are due to be run
     */
    public function runDue()
    {
        foreach ($this->scheduleService->getDueCommands() as $command) {
            if ($command->isEnabled() && $this->runnableInEnvironment($command)) {
                $this->run($command);
            }
        }
    }

    /**
     * Run a scheduled command
     *
     * @param \Indatus\CommandScheduler\ScheduledCommand $command
     */
    public function run(ScheduledCommand $command)
    {
        $backgroundProcess = App::make('Indatus\ScheduledCommand\BackgroundProcess');
        $backgroundProcess->run($command);
    }

    /**
     * Determine if a scheduled command is in the correct environment
     *
     * @param \Indatus\CommandScheduler\ScheduledCommand $command
     * @return bool
     */
    public function runnableInEnvironment(ScheduledCommand $command)
    {
        $environment = $command->environment();

        //if any
        if ($environment == '*' || $environment == App::environment()) {
            return true;
        }

        if (is_array($environment) && in_array(App::environment(), $environment)) {
            return true;
        }

        return false;
    }


}