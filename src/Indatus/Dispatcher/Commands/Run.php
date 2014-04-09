<?php namespace Indatus\Dispatcher\Commands;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Console\Command;
use Indatus\Dispatcher\Services\CommandService;

/**
 * Run any commands that should be run
 * @author Ben Kuhl <bkuhl@indatus.com>
 */
class Run extends Command
{

    /** @var \Indatus\Dispatcher\Services\CommandService|null  */
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