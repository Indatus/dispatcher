<?php
/**
 * @author Ben Kuhl <bkuhl@indatus.com>
 */

class TestCase extends Orchestra\Testbench\TestCase
{

    protected function getPackageProviders()
    {
        return array('Indatus\CommandScheduler\CommandSchedulerServiceProvider');
    }

} 