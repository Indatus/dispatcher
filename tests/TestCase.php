<?php

/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

abstract class TestCase extends Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Indatus\Dispatcher\ServiceProvider'];
    }

    /**
     * Get the path for this package
     *
     * @return string
     */
    protected function getPackagePath()
    {
        return realpath(implode(DIRECTORY_SEPARATOR, [
            __DIR__,
            '..',
            'src',
            'Indatus',
            'Dispatcher'
        ]));
    }
}
