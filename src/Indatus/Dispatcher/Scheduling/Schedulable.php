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

    /** @var array $options */
    protected $options = array();

    /** @var bool Instantiate a new instance when using args() or opts() */
    protected $instantiateNew = true;

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
        $scheduler = $this->getClass();
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

    /**
     * Define options for this command when it runs.
     *
     * @param array $options
     *
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
    public function opts(array $options)
    {
        $scheduler = $this->getClass();
        $scheduler->setOptions($options);
        return $scheduler;
    }

    /**
     * Get the options for this command.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the schedule's options
     *
     * This method is only to be used internally by Dispatcher.
     *
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Get a scheduler class
     *
     * @return $this|Schedulable
     */
    public function getClass()
    {
        //use the existing one if we've already instantiated
        if ($this->instantiateNew === false) {
            return $this;
        }

        $this->instantiateNew = false;

        return $this;
    }
}