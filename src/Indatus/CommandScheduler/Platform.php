<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler;


class Platform {

    /**
     * @var int
     */
    const UNIX = 0;

    /**
     * @var int
     */
    const WINDOWS = 1;

    /**
     * @var int
     */
    private $currentOS;

    public function __construct() {
        if ($this->getPlatform() == self::WINDOWS) {
            $this->currentOS = self::WINDOWS;
        } else {
            $this->currentOS = self::UNIX;
        }
    }

    /**
     * Determine if the current OS is Windows
     * @return bool
     */
    public function isWindows()
    {
        return $this->currentOS == self::WINDOWS;
    }

    /**
     * Determine if the current OS is Unix
     * @return bool
     */
    public function isUnix()
    {
        return $this->currentOS == self::UNIX;
    }

    /**
     * @return mixed
     */
    private function getPlatform()
    {
        if (strncasecmp(PHP_OS, "Win", 3) == 0) {
            return self::WINDOWS;
        } else {
            return self::UNIX;
        }
    }

} 