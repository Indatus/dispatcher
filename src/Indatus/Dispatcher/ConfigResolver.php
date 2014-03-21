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

    public function resolveDriverClass($className)
    {
        try {
            return App::make(Config::get('dispatcher::driver').'\\'.$className);
        } catch (\ReflectionException $e) {
            $driver = ucwords(strtolower(Config::get('dispatcher::driver')));
            return App::make('Indatus\Dispatcher\Drivers\\'.$driver.'\\'.$className);
        }
    }

} 