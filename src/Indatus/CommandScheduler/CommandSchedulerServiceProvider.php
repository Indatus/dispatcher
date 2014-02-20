<?php

namespace Indatus\CommandScheduler;

use Illuminate\Support\ServiceProvider;
use Indatus\CommandScheduler\Commands\ScheduleSummary;
use Indatus\CommandScheduler\Services\ScheduleService;
use App;

class CommandSchedulerServiceProvider extends ServiceProvider
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
		$this->package('indatus/command-scheduler');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        App::bind('Indatus\CommandScheduler\Services\SchedulerService', function ($app) {
                return new ScheduleService(new Table());
            });

        //load the scheduler of the appropriate driver
        App::bind('Indatus\CommandScheduler\Scheduler', function () {
                $schedulerClass = Config::get('indatus/command-scheduler::driver');
                var_dump($schedulerClass);
                exit;
                return new $schedulerClass();
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
        return array('command.scheduled.summary');
	}

    /**
     * Register artisan commands
     */
    private function registerCommands()
    {
        $this->app['command.scheduled.summary'] = $this->app->share(function($app)
            {
                return new ScheduleSummary(App::make('Indatus\CommandScheduler\Services\SchedulerService'));
            });
        $this->commands('command.scheduled.summary');
    }

}