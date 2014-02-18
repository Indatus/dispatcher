<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\LaravelCommandScheduler;

use Illuminate\Console\Command;
use App;

abstract class ScheduledCommand extends Command implements ScheduledCommandInterface
{

    /**
     * @var \Indatus\LaravelCommandScheduler\Scheduler
     */
    private $scheduler;

    public function __construct()
    {
        parent::__construct();

        $this->scheduler = App::make('Indatus\LaravelCommandScheduler\Scheduler');
    }

    /**
     * When a command should run
     */
    abstract public function schedule();

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
     * User to run the command as
     * @return string
     */
    public function user()
    {
        return 'root';
    }

}