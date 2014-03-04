<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

namespace Indatus\CommandScheduler;

use App;
use Config;

class ConfigResolver
{

    public function resolveDriverClass($className)
    {
        try {
            return App::make(Config::get('command-scheduler::driver').'\\'.$className);
        } catch (\ReflectionException $e) {
            $driver = ucwords(strtolower(Config::get('command-scheduler::driver')));
            return App::make('Indatus\CommandScheduler\Drivers\\'.$driver.'\\'.$className);
        }
    }

} 