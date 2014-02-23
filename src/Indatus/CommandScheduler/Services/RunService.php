<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler\Services;

use Indatus\CommandScheduler\ScheduledCommandInterface;

class RunService
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
     * Run all commands
     * @todo test
     * @return void
     */
    public function runCommands()
    {
        foreach ($this->scheduleService->getScheduledCommands() as $command) {
            
        }
    }

}