<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\Dispatcher;

use App;
use Illuminate\Console\Command;

abstract class ScheduledCommand extends Command
{


    /**
     * Unfortunately, this has to be here for unit testing
     *
     * @var string
     */
    protected $name = 'scheduledCommand';

    /**
     * When a command should run
     *
     * @param Schedulable $scheduler
     * @return \Indatus\Dispatcher\Schedulable
     */
    abstract public function schedule(Schedulable $scheduler);

    /**
     * User to run the command as
     *
     * @return string Defaults to false to run as default user
     */
    public function user()
    {
        return false;
    }

    /**
     * Environment(s) under which the given command should run
     *
     * @return string
     */
    public function environment()
    {
        return '*';
    }

}