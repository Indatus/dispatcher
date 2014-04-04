<?php namespace Indatus\Dispatcher\Scheduling;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Indatus\Dispatcher\ConfigResolver;

abstract class Schedulable
{
    protected $configResolver;

    protected $arguments;

    public function __construct(ConfigResolver $configResolver, $arguments = array())
    {
        $this->configResolver = $configResolver;
        $this->arguments = $arguments;
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
        return $this->configResolver->resolveDriverClass('Scheduler', $arguments);
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
}