<?php namespace Indatus\Dispatcher\Services;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;

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
        /** @var \Indatus\Dispatcher\BackgroundProcessRunner $backgroundProcessRunner */
        $backgroundProcessRunner = App::make('Indatus\Dispatcher\BackgroundProcessRunner');

        /** @var \Indatus\Dispatcher\Queue $queue */
        $queue = $this->scheduleService->getQueue();
        foreach ($queue->flush() as $queueItem) {
            $command = $queueItem->getCommand();
            $scheduler = $queueItem->getScheduler();
            if ($command->isEnabled() && $this->runnableInEnvironment($command)) {
                $backgroundProcessRunner->run($command, $scheduler->getArguments());
            }
        }
    }

    /**
     * Determine if a scheduled command is in the correct environment
     *
     * @param \Indatus\Dispatcher\Scheduling\ScheduledCommand $command
     *
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
     * Prepare a command's arguments for command line usage
     *
     * @param array $arguments
     *
     * @return string
     */
    public function prepareArguments(array $arguments)
    {
        $argumentPieces = array();
        foreach ($arguments as $arg => $value) {
            //if it's an array of options, throw them in there as well
            if (is_array($value)) {
                foreach ($value as $argArrayValue) {
                    $argumentPieces[] = '--'.$arg.'="'.addslashes($argArrayValue).'"';
                }
            } else {
                $argument = null;

                //option exists with no value
                if (is_numeric($arg)) {
                    $argument = $value;
                } elseif (!empty($value)) {
                    $argument = $arg.'="'.addslashes($value).'"';
                }

                if (!is_null($argument)) {
                    $argumentPieces[] = '--'.$argument;
                }
            }
        }

        return implode(' ', $argumentPieces);
    }

    /**
     * Get a command to run this application
     *
     * @param \Indatus\Dispatcher\Scheduling\ScheduledCommand $scheduledCommand
     * @param array $arguments
     *
     * @return string
     */
    public function getRunCommand(ScheduledCommand $scheduledCommand, array $arguments = array())
    {
        $commandPieces = array(
            'php',
            base_path().'/artisan',
            $scheduledCommand->getName()
        );

        if (count($arguments) > 0) {
            $commandPieces[] = $this->prepareArguments($arguments);
        }

        $commandPieces[] = '&'; //run in background
        $commandPieces[] = '> /dev/null 2>&1'; //don't show output, errors can be viewed in the Laravel log

        //run the command as a different user
        if (is_string($scheduledCommand->user())) {
            array_unshift($commandPieces, 'sudo -u '.$scheduledCommand->user());
        }

        return implode(' ', $commandPieces);
    }

}