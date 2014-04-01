<?php

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Indatus\Dispatcher;

use App;
use Config;

class ConfigResolver
{

    /**
     * Resolve a class based on the driver configuration
     *
     * @param       $className
     * @param array $arguments
     *
     * @return mixed
     */
    public function resolveDriverClass($className, $arguments = [])
    {
        try {
            return App::make(
                Config::get('dispatcher::driver').'\\'.$className, [
                    $this,
                    $arguments
                ]
            );
        } catch (\ReflectionException $e) {
            $driver = ucwords(strtolower(Config::get('dispatcher::driver')));
            return App::make(
                'Indatus\Dispatcher\Drivers\\'.$driver.'\\'.$className, [
                    $this,
                    $arguments
                ]
            );
        }
    }

} 