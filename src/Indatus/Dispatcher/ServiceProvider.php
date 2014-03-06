<?php

namespace Indatus\Dispatcher;

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
        $resolver = App::make('\Indatus\Dispatcher\ConfigResolver');

        //load the scheduler of the appropriate driver
        App::bind('Indatus\Dispatcher\Schedulable', function () use ($resolver) {
                return $resolver->resolveDriverClass('Scheduler');
            });

        //load the scheduler of the appropriate driver
        App::bind('Indatus\Dispatcher\Services\ScheduleService', function () use ($resolver) {
                return $resolver->resolveDriverClass('ScheduleService');
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
        $this->app['command.scheduled.summary'] = $this->app->share(function($app)
            {
                return App::make('Indatus\Dispatcher\Commands\ScheduleSummary');
            });
        $this->commands('command.scheduled.summary');

        //scheduled:make
        $this->app['command.scheduled.make'] = $this->app->share(function($app)
            {
                return App::make('Indatus\Dispatcher\Commands\Make');
            });
        $this->commands('command.scheduled.make');

        //scheduled:run
        $this->app['command.scheduled.run'] = $this->app->share(function($app)
            {
                return App::make('Indatus\Dispatcher\Commands\Run');
            });
        $this->commands('command.scheduled.run');
    }

}