<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler;

use Illuminate\Console\Command;
use App;

abstract class ScheduledCommand extends Command implements ScheduledCommandInterface
{

    /**
     * Unfortunately, this has to be here for unit testing
     *
     * @var string
     */
    protected $name = 'scheduledCommand';

    /**
     * @var \Indatus\CommandScheduler\Scheduler
     */
    private $scheduler;

    public function __construct()
    {
        parent::__construct();

        $this->scheduler = App::make('Indatus\CommandScheduler\Schedulable');
    }

    /**
     * When a command should run
     */
    abstract public function schedule();

    /**
     * Customize the schedule for this command
     *
     * @return \Indatus\CommandScheduler\Scheduler
     */
    public function getScheduler()
    {
        return $this->scheduler;
    }

    /**
     * User to run the command as
     * @return string
     */
    public function user()
    {
        return 'root';
    }

    /**
     * Environment(s) under which the given command should run
     *
     * Defaults to *
     *
     * @return []|string
     */
    public function environment()
    {
        return '*';
    }


}