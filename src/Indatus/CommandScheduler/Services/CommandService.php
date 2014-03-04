<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler\Services;

use App;
use Indatus\CommandScheduler\ScheduledCommand;

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
        $backgroundProcessRunner = App::make('Indatus\CommandScheduler\BackgroundProcessRunner');
        foreach ($this->scheduleService->getDueCommands() as $command) {
            if ($command->isEnabled() && $this->runnableInEnvironment($command)) {
                $backgroundProcessRunner->run($command);
            }
        }
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

    /**
     * Get a command to run this application
     * @param ScheduledCommand $scheduledCommand
     * @return string
     */
    public function getRunCommand(ScheduledCommand $scheduledCommand)
    {
        $commandPieces = [
            'php',
            base_path().'/artisan',
            $scheduledCommand->getName()
        ];

        //run the command as a different user
        if (is_string($scheduledCommand->user())) {
            array_unshift($commandPieces, 'sudo -u '.$scheduledCommand->user());
        }

        return implode(' ', $commandPieces);
    }

}