<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\LaravelCommandScheduler;

use Illuminate\Console\Command;

abstract class ScheduledCommand extends Command {

    /**
     * @var \Indatus\LaravelCommandScheduler\Scheduler
     */
    private $scheduler;

    public function __construct(Scheduler $scheduler)
    {
        parent::__construct();

        $this->scheduler = $scheduler;
    }

    /**
     * When a command should run
     */
    abstract function schedule();

    /**
     * Customize the schedule for this command
     *
     * @return \Indatus\LaravelCommandScheduler\Scheduler
     */
    public function getScheduler()
    {
        return $this->scheduler;
    }

    /**
     * Should this command be run when scheduled?
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

}

//$this->every(4)->hours()->at()