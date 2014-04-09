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
use Indatus\Dispatcher\Services\ScheduleService;

/**
 * View a summary for all scheduled artisan commands
 * @author Ben Kuhl <bkuhl@indatus.com>
 * @package Indatus\Dispatcher\Commands
 */
class ScheduleSummary extends Command
{

    /** @var \Indatus\Dispatcher\Services\ScheduleService|null  */
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
    protected $name = 'scheduled:summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View a summary of all scheduled artisan commands';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->scheduleService->printSummary();
    }
} 