# Dispatcher

[<img src="https://s3-us-west-2.amazonaws.com/oss-avatars/dispatcher.png"/>](http://indatus.com/company/careers)


Dispatcher allows you to schedule your artisan commands within your [Laravel](http://laravel.com) project, eliminating the need to touch the crontab when deploying.  It also allows commands to run per environment and keeps your scheduling logic where it should be, in your version control.

<!--<img align="left" height="300" src="https://s3-us-west-2.amazonaws.com/oss-avatars/dispatcher_round_readme.png">-->

```php
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;

class MyCommand extends ScheduledCommand {

    //your command name, description etc.

	public function schedule(Schedulable $scheduler)
	{
        //every day at 4:17am
        return $scheduler
            ->daily()
            ->hours(4)
            ->minutes(17);
    }

}
```

---

[![Latest Stable Version](https://poser.pugx.org/indatus/dispatcher/v/stable.png)](https://packagist.org/packages/indatus/dispatcher) [![Total Downloads](https://poser.pugx.org/indatus/dispatcher/downloads.png)](https://packagist.org/packages/indatus/dispatcher) [![Build Status](https://travis-ci.org/Indatus/dispatcher.png?branch=master)](https://travis-ci.org/Indatus/dispatcher) [![Code Coverage](https://scrutinizer-ci.com/g/Indatus/dispatcher/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/Indatus/dispatcher/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Indatus/dispatcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Indatus/dispatcher/?branch=master)

## README Contents

* [Features](#features)
* [Tutorial](#tutorial)
* [Installation](#installation)
* [Usage](#usage)
  * [Generating New Scheduled Commands](#new-commands)
  * [Scheduling Existing Commands](#scheduling-commands)
  * [Running Commands As Users](#commands-as-users)
  * [Environment-Specific Commands](#environment-commands)
  * [Running Commands In Maintenance Mode](#maintenance-mode)
  * [Advanced Scheduling](#advanced-scheduling)
* [Drivers](#drivers)
  * [Cron](#Cron)
* [Custom Drivers](#custom-drivers)
* [FAQ](#faq)

<a name="features" />
## Features

 * Schedule artisan commands to run automatically
 * Scheduling is maintained within your version control system
 * Single source of truth for when and where commands run
 * Schedule commands to run with arguments and options
 * Run commands as other users
 * Run commands in certain environments

<a name="tutorial" />
## Tutorial

By Ben Kuhl at the [Laravel Louisville meetup](http://laravel-louisville.github.io/meetup/) ([@lurvul](https://twitter.com/lurvul)): [Video](http://vimeo.com/94212203) - [Slides](http://bkuhl.github.io/dispatcher-slides)

By Jefferey Way at [Laracasts](https://www.laracasts.com): [Recurring Tasks the Laravel Way](https://laracasts.com/lessons/recurring-tasks-the-laravel-way)

<a name="installation" />
## Installation

Requires:

 * PHP 5.3+ or [HHVM](http://hhvm.com/)
 * [Laravel](http://laravel.com) 4.1+

You can install the library via [Composer](http://getcomposer.org) by adding the following line to the **require** block of your *composer.json* file:

````
"indatus/dispatcher": "1.4.*@dev"
````

Next run `composer update`.

Add this line to the providers array in your `app/config/app.php` file :

```php
        'Indatus\Dispatcher\ServiceProvider',
```

To use with Cron, see the [Cron driver](#Cron) section.

<a name="usage" />
## Usage

```
scheduled
  scheduled:make              Create a new scheduled artisan command
  scheduled:run               Run scheduled commands
  scheduled:summary           View a summary of all scheduled artisan commands
```

If commands are not visible via `php artisan` then they cannot be scheduled.

<a name="new-commands" />
### Generating New Scheduled Commands

Use `php artisan scheduled:make` to generate a new scheduled command, the same way you would use artisan's `command:make`.  Then [register your command](http://laravel.com/docs/commands#registering-commands) with Laravel.

<a name="scheduling-commands" />
### Scheduling Existing Commands

You may either implement `\Indatus\Dispatcher\Scheduling\ScheduledCommandInterface` or follow the below steps.

1. Extend `\Indatus\Dispatcher\Scheduling\ScheduledCommand`
2. Add use statements to your command.  If you're using a custom driver you will use a different `Scheduler` class.
```php
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\Cron\Scheduler;
```
3. Implement schedule():
```php
	/**
	 * When a command should run
	 *
	 * @param Scheduler $scheduler
	 * @return \Indatus\Dispatcher\Scheduling\Schedulable
	 */
	public function schedule(Schedulable $scheduler)
	{
		return $scheduler;
    }
```

For details and examples on how to schedule, see [Cron](#Cron).

<a name="commands-as-users" />
### Running Commands As Users

You may override `user()` to run a given artisan command as a specific user.  Ensure your `scheduled:run` artisan command is running as root.

```php
    public function user()
    {
        return 'backup';
    }
```

> This feature may not be supported by all drivers.

<a name="environment-commands" />
### Environment-Specific Commands

You may override `environment()` to ensure your command is only scheduled in specific environments.  It should provide a single environment or an array of environments.

```php
    public function environment()
    {
        return ['development','staging'];
    }
```

<a name="maintenance-mode" />
### Maintenance Mode

By default, cron commands will *not* run when application is in Maintenance Mode. This will prevent all sorts of weird output that might occur if a cron command is run while you are migrating a database or doing a composer update.

You may override `runInMaintenanceMode()` to force your command to still be run while the application is in maintenance mode.

```php
    public function runInMaintenanceMode()
    {
        return true;
    }
```

<a name="advanced-scheduling" />
### Advanced scheduling

> These examples utilize the [cron](#Cron) driver.

You may schedule a given command to to run at multiple times by `schedule()` returning multiple `Schedulable` instances.

```php
	public function schedule(Schedulable $scheduler)
	{
        return [
            // 5am Mon-Fri
            $scheduler->everyWeekday()->hours(5),

            // 2am every Saturday
            App::make(get_class($scheduler))
                ->daysOfTheWeek(Scheduler::SATURDAY)
                ->hours(2)
        ];
    }
```

You may also schedule a command to run with arguments and options.

```php

	public function schedule(Schedulable $scheduler)
	{
		return [
            // equivalent to: php /path/to/artisan command:name /path/to/file
            $scheduler->args(['/path/to/file'])
                ->everyWeekday()
                ->hours(5),

            // equivalent to: php /path/to/artisan command:name /path/to/file --force --toDelete="expired" --exclude="admins" --exclude="developers"
            $scheduler->args(['/path/to/file'])
                ->opts([
                    'force',
                    'toDelete' => 'expired',
                    'exclude' => [
                        'admins',
                        'developers'
                    ]
                ])
                ->daysOfTheMonth([1, 15])
                ->hours(2)
        ];
	}
```

> Both `args()` and `opts()`, whichever is called first, will internally create a new `Schedulable` instance for you so you don't need to `App::make()`.

<a name="drivers" />
## Drivers

While Cron is the default driver for Dispatcher, it can be used with any scheduling tool that can run artisan commands. See [building custom drivers](#custom-drivers).

<a name="Cron" />
### Cron (Default)

Add the following to your root Crontab (`sudo crontab -e`):

```php
* * * * * php /path/to/artisan scheduled:run 1>> /dev/null 2>&1
```

If you are adding this to `/etc/cron.d` you'll need to specify a user immediately after `* * * * *`.

> If you'd like for scheduled commands to be able to run as different users, be sure to add this to the root Crontab.  Otherwise all commands run as the user whose Crontab you've added this to.

Examples of how to schedule:

```php
	public function schedule(Schedulable $scheduler)
	{
        //every day at 4:17am
        return $scheduler->daily()->hours(4)->minutes(17);
    }
```


```php
	public function schedule(Schedulable $scheduler)
	{
        //every Tuesday/Thursday at 5:03am
        return $scheduler->daysOfTheWeek([
                Scheduler::TUESDAY,
                Scheduler::THURSDAY
            ])->hours(5)->minutes(3);
    }
```

You may also schedule commands via raw Cron expressions

```php
	public function schedule(Schedulable $scheduler)
	{
        //every other day at 3:15am, 4:15am and 5:15am
        return $scheduler->setSchedule(15, [3,4,5], '*/2', '*', '*');
    }
```

<a name="custom-drivers" />
## Custom Drivers

You can build your own drivers or extend a driver that's included.  Create a packagepath such as `\MyApp\ScheduleDriver\` and create two classes:

 * `Scheduler` that `implements Indatus\Dispatcher\Scheduling\Schedulable`.  This class should provide a useful interface for programmers to schedule their commands.
 * `ScheduleService` that `extends \Indatus\Dispatcher\Services\ScheduleService`.  This class contains logic on how to determine if a command is due to run.

Publish the configs using `php artisan config:publish indatus/dispatcher`. Then update your driver configuration to reference the package in which these 2 classes are included (do not include a trailing slash):

```php
    'driver' => '\MyApp\ScheduleDriver'
```

<a name="faq" />
## FAQ

**I need to deploy to multiple servers representing a single environment.  How can I be sure my command is only run by a single server and not run on each server?**

Schedule `scheduled:run` to run every minute with [rcron](https://code.google.com/p/rcron/):

```php
* * * * * /usr/bin/rcron php /path/to/artisan scheduled:run 1>> /dev/null 2>&1
```

**Why are my commands not running when I've scheduled them correctly?  I'm also not seeing any error output**

1) Verify that mcrypt is installed and working correctly via the command `php -i | mcrypt`.

2) Utilizing `php artisan scheduled:run --debug` will tell you why they're not running.  If you do not see your command listed here then it is not set up correctly.

Example:

```
$ php artisan scheduled:run --debug                                                                                        
Running commands...
     backup:avatars: No schedules were due
     command:name: No schedules were due
     myTestCommand:name: No schedules were due
     cache:clean: /usr/bin/env php /Users/myUser/myApp/artisan cache:clean > /dev/null &
     mail:subscribers: /usr/bin/env php /Users/myUser/myApp/artisan mail:subscribers > /dev/null &
```

**I have commands that extend `ScheduledCommand` but why don't they appear in when I run `scheduled:summary`?**

Commands that are disabled will not appear here.  Check and be sure `isEnabled()` returns true on those commands.
