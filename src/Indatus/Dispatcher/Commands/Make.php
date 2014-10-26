<?php namespace Indatus\Dispatcher\Commands;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App;
use Config;
use Illuminate\Foundation\Console\ConsoleMakeCommand;

/**
 * View a summary for all scheduled artisan commands
 * @author Ben Kuhl <bkuhl@indatus.com>
 */
class Make extends ConsoleMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'scheduled:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new scheduled artisan command';

    /**
     * @param string $name
     * @codeCoverageIgnore
     */
    protected function buildClass($name)
    {
        return $this->extendStub(parent::buildClass($name));
    }

    /**
     * Make sure we're implementing our own class
     * @param $stub
     * @return string
     */
    protected function extendStub($stub)
    {
        /** @var \Illuminate\Filesystem\Filesystem $files */
        $files = App::make('Illuminate\Filesystem\Filesystem');
        $content = $files->get(__DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'command.stub');

        $replacements = [
            'use Illuminate\Console\Command' => "use Indatus\\Dispatcher\\Scheduling\\ScheduledCommand;\n".
                "use Indatus\\Dispatcher\\Scheduling\\Schedulable;\n".
                "use Indatus\\Dispatcher\\Drivers\\".ucwords(Config::get('dispatcher::driver'))."\\Scheduler",
            'extends Command {' => 'extends ScheduledCommand {',
            'parent::__construct();' => $content,
        ];

        $stub = str_replace(array_keys($replacements), array_values($replacements), $stub);

        return $stub;
    }
}
