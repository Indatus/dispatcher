<?php namespace Indatus\Dispatcher;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Queue
{
    /** @var QueueItem[] */
    protected static $queue = array();

    /**
     * Add an item to the queue
     * @param QueueItem $item
     */
    public function add(QueueItem $item)
    {
        self::$queue[] = $item;
    }

    /**
     * The size of the queue
     * @return int
     */
    public function size()
    {
        return count(self::$queue);
    }

    /**
     * Get all items in the queue
     * @return QueueItem[]
     */
    public function flush()
    {
        $queue = self::$queue;
        self::$queue = array();

        return $queue;
    }
}
