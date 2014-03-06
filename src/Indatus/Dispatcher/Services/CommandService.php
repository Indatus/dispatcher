<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\Dispatcher\Services;

use App;
use Indatus\Dispatcher\ScheduledCommand;

class CommandService
{

    /**
     * @var /Indatus\Dispatcher\Services\ScheduleService
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
        $backgroundProcessRunner = App::make('Indatus\Dispatcher\BackgroundProcessRunner');
        foreach ($this->scheduleService->getDueCommands() as $command) {
            if ($command->isEnabled() && $this->runnableInEnvironment($command)) {
                $backgroundProcessRunner->run($command);
            }
        }
    }

    /**
     * Determine if a scheduled command is in the correct environment
     *
     * @param \Indatus\Dispatcher\ScheduledCommand $command
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
            $scheduledCommand->getName(),
            '&', //run in background
            '> /dev/null 2>&1' //don't show output, errors can be viewed in the Laravel log
        ];

        //run the command as a different user
        if (is_string($scheduledCommand->user())) {
            array_unshift($commandPieces, 'sudo -u '.$scheduledCommand->user());
        }

        return implode(' ', $commandPieces);
    }

}