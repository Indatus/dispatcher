<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler;

use Indatus\CommandScheduler\Exception;
use \App;

class BackgroundProcess
{
    /**
     * The user to run the process as
     * @var string
     */
    private $runAs;

    public function __construct()
    {

    }

    public function run(ScheduledCommand $scheduledCommand)
    {

    }

    public function runAs($user)
    {
        $platform = App::make('Indatus\CommandScheduler\Platform');
        if ($platform->isWindows()) {
            throw new Exception('Unable to run command as another user on Windows');
        }

        if (!$this->isRoot()) {
            throw new Exception('Must be run as root to run command as another user');
        }

        $this->runAs = $user;
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