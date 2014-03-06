<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\Dispatcher\Services;

use App;

class BackgroundProcessService
{

    /**
     * @var \Indatus\Dispatcher\Services\ScheduleService
     */
    private $scheduleService;

    /**
     * @var \Indatus\Dispatcher\Services\CommandService
     */
    private $commandService;

    function __construct(ScheduleService $scheduleService, CommandService $commandService)
    {
        $this->scheduleService = $scheduleService;
        $this->commandService = $commandService;
    }

    /**
     * Determine if the background process can run as another user
     * @return bool
     */
    public function canRunAsUser()
    {
        $platform = App::make('Indatus\Dispatcher\Platform');

        return !$platform->isWindows() && $this->isRoot();
    }

    /**
     * Is the current command being run as root?
     * @return bool
     */
    private function isRoot()
    {
        return (posix_getuid() === 0);
    }

}