<?php namespace Indatus\Dispatcher;

/**
 * @copyright   2014 Indatus
 * @package Indatus\Dispatcher
 */

class OptionReader
{
    /** @var array */
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Determine if we're in debug mode
     *
     * @return bool
     */
    public function isDebugMode()
    {
        return array_key_exists('debug', $this->options) && $this->options['debug'] === true;
    }
}
