<?php namespace Indatus\Dispatcher;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @codeCoverageIgnore
 */
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
     * Determine if the current OS is Windows
     * @return bool
     */
    public function isWindows()
    {
        return $this->getPlatform() == self::WINDOWS;
    }

    /**
     * Determine if the current OS is Unix
     * @return bool
     */
    public function isUnix()
    {
        return $this->getPlatform() == self::UNIX;
    }

    /**
     * @return integer
     */
    private function getPlatform()
    {
        if (strncasecmp(PHP_OS, "Win", 3) == 0) {
            return self::WINDOWS;
        } else {
            return self::UNIX;
        }
    }

    /**
     * Determine if we're running in HHVM
     *
     * @return bool
     */
    public function isHHVM()
    {
        return defined('HHVM_VERSION');
    }

}
