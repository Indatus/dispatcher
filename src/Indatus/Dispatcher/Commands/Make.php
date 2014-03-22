<?php

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Indatus\Dispatcher\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Console\CommandMakeCommand;

/**
 * View a summary for all scheduled artisan commands
 * @author Ben Kuhl <bkuhl@indatus.com>
 * @package Indatus\Dispatcher\Commands
 */
class Make extends CommandMakeCommand
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
     * @param string $file
     * @param string $stub
     * @codeCoverageIgnore
     */
    protected function writeCommand($file, $stub) {
        return parent::writeCommand($file, $this->extendStub($stub));
    }

    /**
     * Make sure we're implementing our own class
     * @param $stub
     * @return string
     */
    protected function extendStub($stub)
    {
        $replacements = array(
            'use Illuminate\Console\Command' => "use Indatus\\Dispatcher\\ScheduledCommand;\n".
                "use Indatus\\Dispatcher\\Scheduler",
            'extends Command {' => 'extends ScheduledCommand {',
            'parent::__construct();' => $this->getStub()
        );

        $stub = str_replace(array_keys($replacements), array_values($replacements), $stub);

        return $stub;
    }

    /**
     * Get our own stub
     */
    private function getStub()
    {
        return file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'stubs'.DIRECTORY_SEPARATOR.'command.stub');
    }
} 