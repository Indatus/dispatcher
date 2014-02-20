<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */
namespace Indatus\CommandScheduler\Services;

interface ScheduleServiceInterface
{
    /**
     * Get all commands that are scheduled
     * @todo test
     * @return array
     */
    public function getScheduledCommands();

    /**
     * Review scheduled commands schedule, active status, etc.
     *
     * @return void
     */
    public function printSummary();
}