<?php namespace Indatus\Dispatcher;

use Indatus\Dispatcher\Scheduling\ScheduledCommandInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @copyright   2014 Indatus
 * @package Indatus\Dispatcher
 */

class Debugger
{
    /** @var \Symfony\Component\Console\Output\OutputInterface */
    protected $output;

    /** @var \Indatus\Dispatcher\OptionReader */
    protected $optionReader;

    function __construct(OptionReader $optionReader, OutputInterface $output)
    {
        $this->output = $output;
        $this->optionReader = $optionReader;
    }

    /**
     * Indicate why a command was not run
     *
     * @param ScheduledCommandInterface $command
     * @param                           $reason
     */
    public function commandNotRun(ScheduledCommandInterface $command, $reason) {
        if ($this->optionReader->isDebugMode()) {
            $this->output->writeln('     <comment>'.$command->getName().'</comment>: '.$reason);
        }
    }

    /**
     * Indicate why a command has run
     *
     * @param ScheduledCommandInterface $command
     * @param                           $runCommand
     */
    public function commandRun(ScheduledCommandInterface $command, $runCommand) {
        if ($this->optionReader->isDebugMode()) {
            $this->output->writeln('     <info>'.$command->getName().'</info>: '.$runCommand);
        }
    }

    /**
     * Log plain text
     *
     * @param $message
     */
    public function log($message)
    {
        if ($this->optionReader->isDebugMode()) {
            $this->output->writeln($message);
        }
    }
}
