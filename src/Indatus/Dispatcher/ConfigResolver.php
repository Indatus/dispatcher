<?php namespace Indatus\Dispatcher;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use ReflectionException;

class ConfigResolver
{
    /** @var Repository */
    protected $config;

    /** @var Container */
    protected $container;

    public function __construct(Repository $config, Container $container)
    {
        $this->config = $config;
        $this->container = $container;
    }

    /**
     * Resolve a class based on the driver configuration
     *
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
    public function resolveSchedulerClass()
    {
        try {
            return $this->container->make($this->getDriver().'\\Scheduler', [$this]);
        } catch (ReflectionException $e) {
            return $this->container->make('Indatus\Dispatcher\Drivers\\'.$this->getDriver().'\\Scheduler', [$this]);
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
            return $this->container->make($this->getDriver().'\\ScheduleService');
        } catch (ReflectionException $e) {
            return $this->container->make('Indatus\Dispatcher\Drivers\\'.$this->getDriver().'\\ScheduleService');
        }
    }

    /**
     * Get the dispatcher driver class
     *
     * @return string
     */
    public function getDriver()
    {
        return ucfirst($this->config->get('dispatcher::driver'));
    }
}
