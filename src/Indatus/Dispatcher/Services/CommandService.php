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

            /** @var \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface $command */
            $command = $queueItem->getCommand();

            if ($command->isEnabled() && $this->runnableInEnvironment($command)) {
                $scheduler = $queueItem->getScheduler();

                $backgroundProcessRunner->run($command, $scheduler->getArguments(), $scheduler->getOptions());
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
        return implode(' ', $arguments);
    }

    /**
     * Prepare a command's options for command line usage
     *
     * @param array $options
     *
     * @return string
     */
    public function prepareOptions(array $options)
    {
        $optionPieces = array();
        foreach ($options as $opt => $value) {
            //if it's an array of options, throw them in there as well
            if (is_array($value)) {
                foreach ($value as $optArrayValue) {
                    $optionPieces[] = '--'.$opt.'="'.addslashes($optArrayValue).'"';
                }
            } else {
                $option = null;

                //option exists with no value
                if (is_numeric($opt)) {
                    $option = $value;
                } elseif (!empty($value)) {
                    $option = $opt.'="'.addslashes($value).'"';
                }

                if (!is_null($option)) {
                    $optionPieces[] = '--'.$option;
                }
            }
        }

        return implode(' ', $optionPieces);
    }

    /**
     * Get a command to run this application
     *
     * @param \Indatus\Dispatcher\Scheduling\ScheduledCommand $scheduledCommand
     * @param array $arguments
     * @param array $options
     *
     * @return string
     */
    public function getRunCommand(
        ScheduledCommand $scheduledCommand,
        array $arguments = array(),
        array $options = array())
    {
        /** @var \Indatus\Dispatcher\Platform $platform */
        $platform = App::make('Indatus\Dispatcher\Platform');

        $commandPieces = array(
            'php',
            base_path().'/artisan',
            $scheduledCommand->getName()
        );

        if (count($arguments) > 0) {
            $commandPieces[] = $this->prepareArguments($arguments);
        }

        if (count($options) > 0) {
            $commandPieces[] = $this->prepareOptions($options);
        }

        if ($platform->isUnix()) {
            $commandPieces[] = '> /dev/null'; //don't show output, errors can be viewed in the Laravel log
            $commandPieces[] = '&'; //run in background

            //run the command as a different user
            if (is_string($scheduledCommand->user())) {
                array_unshift($commandPieces, 'sudo -u '.$scheduledCommand->user());
            }
        } elseif($platform->isWindows()) {
            $commandPieces[] = '> NUL'; //don't show output, errors can be viewed in the Laravel log

            //run in background on windows
            array_unshift($commandPieces, '/B');
            array_unshift($commandPieces, 'START');
        }

        return implode(' ', $commandPieces);
    }

}