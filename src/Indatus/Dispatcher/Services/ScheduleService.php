<?php

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Indatus\Dispatcher\Services;

use App;
use Artisan;
use Indatus\Dispatcher\Scheduling\ScheduledCommandInterface;
use Indatus\Dispatcher\Table;

abstract class ScheduleService
{

    /** @var  \Indatus\Dispatcher\Table */
    protected $table;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Determine if a command is due to be run

*
*@param \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface $command

*
*@return bool
     */
    abstract public function isDue(ScheduledCommandInterface $command);

    /**
     * Get all commands that are scheduled
     *
     * @return array
     */
    public function getScheduledCommands()
    {
        $scheduledCommands = array();
        foreach (Artisan::all() as $command) {
            if ($command instanceOf ScheduledCommandInterface) {
                $scheduledCommands[] = $command;
            }
        }

        return $scheduledCommands;
    }

    /**
     * Get all commands that are due to be run
     *
     * @return \Indatus\Dispatcher\Scheduling\ScheduledCommand[]
     */
    public function getDueCommands()
    {
        $commands = array();
        foreach ($this->getScheduledCommands() as $command) {
            if ($this->isDue($command)) {
                $commands[] = $command;
            }
        }
        return $commands;
    }

    /**
     * Review scheduled commands schedule, active status, etc.
     * @return void
     */
    abstract public function printSummary();

}