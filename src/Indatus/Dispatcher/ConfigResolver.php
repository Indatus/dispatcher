<?php namespace Indatus\Dispatcher;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use ReflectionException;

class ConfigResolver
{
    /** @var Repository */
    protected $config;

    /** @var Application */
    protected $app;

    public function __construct(Repository $config, Application $app)
    {
        $this->config = $config;
        $this->app = $app;
    }

    /**
     * Resolve a class based on the driver configuration
     *
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
    public function resolveSchedulerClass()
    {
        try {
            return $this->app->make($this->getDriver().'\\Scheduler', [$this]);
        } catch (ReflectionException $e) {
            return $this->app->make('Indatus\Dispatcher\Drivers\\'.$this->getDriver().'\\Scheduler', [$this]);
        }
    }

    /**
     * Resolve a class based on the driver configuration
     *
     * @return \Indatus\Dispatcher\Scheduling\ScheduleService
     */
    public function resolveServiceClass()
    {
        try {
            return $this->app->make($this->getDriver().'\\ScheduleService');
        } catch (ReflectionException $e) {
            return $this->app->make('Indatus\Dispatcher\Drivers\\'.$this->getDriver().'\\ScheduleService');
        }
    }

    /**
     * Get the dispatcher driver class
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->config->get('dispatcher::driver');
    }
}
