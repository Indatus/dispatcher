<?php namespace Indatus\Dispatcher\Scheduling;

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

use Indatus\Dispatcher\Scheduler;

interface ScheduledCommandInterface
{
    /**
     * Get the name of the command
     * @return string
     */
    public function getName();

    /**
     * User to run the command as
     * @return string Defaults to false to run as default user
     */
    public function user();

    /**
     * When a command should run
     * @param Scheduler $scheduler
     * @return \Indatus\Dispatcher\Scheduling\Schedulable|\Indatus\Dispatcher\Scheduling\Schedulable[]
     */
    public function schedule(Schedulable $scheduler);

    /**
     * Environment(s) under which the given command should run
     * Defaults to '*' for all environments
     * @return string|array
     */
    public function environment();

    /**
     * Checks whether the command is enabled or not in the current environment
     * Override this to check for x or y and return false if the command can not
     * run properly under the current conditions.
     * @return Boolean
     */
    public function isEnabled();
}
