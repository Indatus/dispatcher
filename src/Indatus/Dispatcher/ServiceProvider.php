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
use Config;

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
	}

	/**
	 * Register the service provider.
	 *
     * @codeCoverageIgnore
	 * @return void
	 */
	public function register()
	{
        /** @var \Indatus\Dispatcher\ConfigResolver $resolver */
        $resolver = App::make('\Indatus\Dispatcher\ConfigResolver');

        //load the scheduler of the appropriate driver
        App::bind('Indatus\Dispatcher\Scheduling\Schedulable', function () use ($resolver) {
            return $resolver->resolveSchedulerClass();
        });

        //load the schedule service of the appropriate driver
        App::bind('Indatus\Dispatcher\Services\ScheduleService', function () use ($resolver) {
            return $resolver->resolveServiceClass();
        });

        $this->registerCommands();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
        return array(
            'command.scheduled.summary',
            'command.scheduled.make',
            'command.scheduled.run'
        );
	}

    /**
     * Register artisan commands
     * @codeCoverageIgnore
     */
    private function registerCommands()
    {
        //scheduled:summary
        $this->app['command.scheduled.summary'] = $this->app->share(function() {
            return App::make('Indatus\Dispatcher\Commands\ScheduleSummary');
        });
        $this->commands('command.scheduled.summary');

        //scheduled:make
        $this->app['command.scheduled.make'] = $this->app->share(function() {
            return App::make('Indatus\Dispatcher\Commands\Make');
        });
        $this->commands('command.scheduled.make');

        //scheduled:run
        $this->app['command.scheduled.run'] = $this->app->share(function() {
            return App::make('Indatus\Dispatcher\Commands\Run');
        });
        $this->commands('command.scheduled.run');
    }

}
