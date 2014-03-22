<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

class TestCase extends Orchestra\Testbench\TestCase
{

    protected function getPackageProviders()
    {
        return array('Indatus\Dispatcher\ServiceProvider');
    }

    /**
     * Get the path for this package
     *
     * @return string
     */
    protected function getPackagePath()
    {
        return realpath(implode(DIRECTORY_SEPARATOR, array(
            __DIR__,
            '..',
            'src',
            'Indatus',
            'Dispatcher'
        )));
    }

} 