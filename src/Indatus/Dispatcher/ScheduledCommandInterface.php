<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */
namespace Indatus\Dispatcher;

interface ScheduledCommandInterface
{
    /**
     * User to run the command as
     * @return string Defaults to false to run as default user
     */
    public function user();

    /**
     * When a command should run
     * @param Schedulable $scheduler
     * @return \Indatus\Dispatcher\Schedulable
     */
    public function schedule(Schedulable $scheduler);

    /**
     * Environment(s) under which the given command should run
     * Defaults to '*' for all environments
     * @return string
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