<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\LaravelCommandScheduler\Commands;

use Illuminate\Console\Command;
use Indatus\LaravelCommandScheduler\Services\ScheduleService;

/**
 * View a summary for all scheduled artisan commands
 * @author Ben Kuhl <bkuhl@indatus.com>
 * @package Indatus\LaravelCommandScheduler\Commands
 */
class ScheduleSummary extends Command
{

    /** @var \Indatus\LaravelCommandScheduler\Services\ScheduleService|null  */
    private $scheduleService = null;

    public function __construct(ScheduleService $scheduleService)
    {
        parent::__construct();

        $this->scheduleService = $scheduleService;
    }

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'crontab:summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View a summary for all scheduled artisan commands';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $output = $this->scheduleService->getSummary();

        //@todo does this comment need to be here?
        $this->comment($output);
    }
} 