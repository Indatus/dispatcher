<?php

namespace Indatus\LaravelCommandScheduler;

use Illuminate\Support\ServiceProvider;
use Indatus\LaravelCommandScheduler\Commands\ScheduleSummary;
use Indatus\LaravelCommandScheduler\Services\ScheduleService;
use App;

class LaravelCommandSchedulerServiceProvider extends ServiceProvider
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
		$this->package('indatus/laravel-command-scheduler');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        App::bind('Indatus\LaravelCommandScheduler\Services\SchedulerService', function ($app) {
                return new ScheduleService();
            });

        $this->app['command.crontab.summary'] = $this->app->share(function($app)
            {
                return new ScheduleSummary(App::make('Indatus\LaravelCommandScheduler\Services\SchedulerService'));
            });
        $this->commands('command.crontab.summary');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
        return array('command.crontab.summary');
	}

}