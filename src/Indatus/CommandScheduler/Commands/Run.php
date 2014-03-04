<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler\Commands;

use Illuminate\Console\Command;
use Indatus\CommandScheduler\Services\CommandService;

/**
 * Run any commands that should be run
 * @author Ben Kuhl <bkuhl@indatus.com>
 * @package Indatus\CommandScheduler\Commands
 */
class Run extends Command
{

    /** @var \Indatus\CommandScheduler\Services\CommandService|null  */
    private $commandService = null;

    public function __construct(CommandService $commandService)
    {
        parent::__construct();

        $this->commandService = $commandService;
    }

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'scheduled:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run scheduled commands';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->commandService->runDue();
    }
} 