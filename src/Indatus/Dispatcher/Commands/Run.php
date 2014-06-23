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
use Symfony\Component\Console\Input\InputOption;
use App;

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
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('debug', 'd', InputOption::VALUE_NONE, 'Output debug information about why commands do/don\'t run.'),
        );
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        /** @var \Indatus\Dispatcher\OptionReader $optionReader */
        $optionReader = App::make('Indatus\Dispatcher\OptionReader', array(
                $this->option()
            ));

        /** @var \Indatus\Dispatcher\Debugger $debugger */
        $debugger = App::make('Indatus\Dispatcher\Debugger', array(
                $optionReader,
                $this->getOutput()
            ));

        $this->commandService->runDue($debugger);
    }
} 
