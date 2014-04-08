<?php namespace Indatus\Dispatcher\Scheduling;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App;
use Indatus\Dispatcher\ConfigResolver;
use Symfony\Component\Console\Input\ArgvInput;

abstract class Schedulable
{
    /** @var \Indatus\Dispatcher\ConfigResolver $configResolver */
    protected $configResolver;

    /** @var array $arguments */
    protected $arguments = array();

    public function __construct(ConfigResolver $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * Define arguments for this command when it runs.
     *
     * @param array $arguments
     *
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
    public function args(array $arguments)
    {
        /** @var \Indatus\Dispatcher\Scheduling\Schedulable $scheduler */
        $scheduler = $this->configResolver->resolveSchedulerClass();
        $scheduler->setArguments($arguments);
        return $scheduler;
    }

    /**
     * Get the arguments for this command.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Set the schedule's arguments
     *
     * This method is only to be used internally by Dispatcher.
     *
     * @param array $arguments
     */
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }
}