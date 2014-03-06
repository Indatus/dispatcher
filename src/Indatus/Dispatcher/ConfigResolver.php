<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
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