<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler;

use App;
use Indatus\CommandScheduler\Services\CommandService;

class BackgroundProcessRunner
{

    /**
     * @var \Indatus\CommandScheduler\Services\CommandService
     */
    private $commandService;

    public function __construct(CommandService $commandService)
    {
        $this->commandService = $commandService;
    }

    /**
     * Run a scheduled command
     *
     * @param ScheduledCommand $scheduledCommand
     * @return bool
     */
    public function run(ScheduledCommand $scheduledCommand)
    {
        exec($this->commandService->getRunCommand($scheduledCommand));

        return true;
    }
} 