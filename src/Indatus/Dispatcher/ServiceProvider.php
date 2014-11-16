<?php namespace Indatus\Dispatcher;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App;
use Illuminate\Foundation\Application;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('indatus/dispatcher');

        $resolver = $this->app->make('\Indatus\Dispatcher\ConfigResolver');

        //load the scheduler of the appropriate driver
        $this->app->bind('Indatus\Dispatcher\Scheduling\Schedulable', function () use ($resolver) {
            return $resolver->resolveSchedulerClass();
        });

        //load the schedule service of the appropriate driver
        $this->app->bind('Indatus\Dispatcher\Services\ScheduleService', function () use ($resolver) {
            return $resolver->resolveServiceClass();
        });
    }

    /**
     * Register the service provider.
     *
     * @codeCoverageIgnore
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.scheduled.summary',
            'command.scheduled.make',
            'command.scheduled.run'
        ];
    }

    /**
     * Register artisan commands
     * @codeCoverageIgnore
     */
    private function registerCommands()
    {
        //scheduled:summary
        $this->app->bindShared('command.scheduled.summary', function (Application $app) {
            return $app->make('Indatus\Dispatcher\Commands\ScheduleSummary');
        });

        //scheduled:make
        $this->app->bindShared('command.scheduled.make', function (Application $app) {
            return $app->make('Indatus\Dispatcher\Commands\Make');
        });

        //scheduled:run
        $this->app->bindShared('command.scheduled.run', function (Application $app) {
            return $app->make('Indatus\Dispatcher\Commands\Run');
        });

        $this->commands($this->provides());
    }
}
