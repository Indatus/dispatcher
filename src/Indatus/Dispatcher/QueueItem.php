<?php namespace Indatus\Dispatcher;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class QueueItem
{
    /** @var \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface */
    protected $command;

    /** @var \Indatus\Dispatcher\Scheduling\Schedulable */
    protected $scheduler;

    /**
     * @return \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return \Indatus\Dispatcher\Scheduling\Schedulable
     */
    public function getScheduler()
    {
        return $this->scheduler;
    }

    /**
     * @param \Indatus\Dispatcher\Scheduling\Schedulable $scheduler
     */
    public function setScheduler($scheduler)
    {
        $this->scheduler = $scheduler;
    }
}
