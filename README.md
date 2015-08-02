# Dispatcher

<!--[<img src="https://s3-us-west-2.amazonaws.com/oss-avatars/dispatcher.png"/>](http://indatus.com/company/careers)-->


Dispatcher allows you to schedule your artisan commands within your [Laravel](http://laravel.com) project, eliminating the need to touch the crontab when deploying.  It also allows commands to run per environment and keeps your scheduling logic where it should be, in your version control.

<img align="left" height="300" src="https://s3-us-west-2.amazonaws.com/oss-avatars/dispatcher_round_readme.png">

```php
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\DateTime\Scheduler;

class MyCommand extends ScheduledCommand {
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
  * [For Laravel 4 (see 1.4 branch)](https://github.com/Indatus/dispatcher/tree/1.4#installation)
  * [For Laravel 5](#installation) - discontinued, see [Laravel 5's scheduler](http://laravel-news.com/2014/11/laravel-5-scheduler/)
  * [Upgrading from 1.4 to 2.0](#upgrading-1.4-2.0)
* [Usage](#usage)
  * [Generating New Scheduled Commands](#new-commands)
  * [Scheduling Existing Commands](#scheduling-commands)
  * [Running Commands As Users](#commands-as-users)
  * [Environment-Specific Commands](#environment-commands)
  * [Running Commands In Maintenance Mode](#maintenance-mode)
  * [Advanced Scheduling](#advanced-scheduling)
* [Drivers](#drivers)
  * [DateTime](#datetime)
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
 * Use custom drivers for custom scheduling contexts

<a name="tutorial" />
## Tutorial

By Ben Kuhl at the [Laravel Louisville meetup](http://laravel-louisville.github.io/meetup/) ([@lurvul](https://twitter.com/lurvul)): [Video](http://vimeo.com/94212203) - [Slides](http://bkuhl.github.io/dispatcher-slides)

By Jefferey Way at [Laracasts](https://www.laracasts.com): [Recurring Tasks the Laravel Way](https://laracasts.com/lessons/recurring-tasks-the-laravel-way)

<a name="installation" />
## Installation

> **NOTICE: [Laravel 5 now includes scheduling](http://laravel-news.com/2014/11/laravel-5-scheduler/) out of the box.  This package will no longer be maintained for Laravel 5 and above**

| Requirements                  | 1.4.*                         | 2.*                               |
|-------------------------------|-------------------------------|-----------------------------------|
| [Laravel](http://laravel.com) | 4.1/4.2                       | 5.x                               |
| [PHP](https://php.net)        | 5.3+                          | 5.4+                              |
| [HHVM](http://hhvm.com)       | 3.3+                          | 3.3+                              |
| Install with Composer...      | ~1.4                          | ~2.0@dev                          |

> If you're using **Laravel 4** view the [readme in the 1.4 branch](https://github.com/Indatus/dispatcher/tree/1.4)

Add this line to the providers array in your `config/app.php` file :

```php
        'Indatus\Dispatcher\ServiceProvider',
```

Add the following to your root Crontab (via `sudo crontab -e`):

```php
* * * * * php /path/to/artisan scheduled:run 1>> /dev/null 2>&1
```

If you are adding this to `/etc/cron.d` you'll need to specify a user immediately after `* * * * *`.

> You may add this to any user's Crontab, but only the root crontab can run commands as other users.

<a name="upgrading-1.4-2.0" />
### Upgrading from 1.4 to 2.0

In all scheduled commands...

 * Replace `use Indatus\Dispatcher\Drivers\Cron\Scheduler` with `use Indatus\Dispatcher\Drivers\DateTime\Scheduler`
 * Replaced uses of `Scheduler::[DAY_OF_WEEK]` with `Day::[DAY_OF_WEEK]` and `Scheduler::[MONTH_OF_YEAR]` with `Month::[MONTH_OF_YEAR]`
 * `executable` config option has been removed.  Dispatcher now inherits the [path to the binary](http://php.net/manual/en/reserved.constants.php#constant.php-binary) that was initially used to run `scheduled:run`

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

1. Add use statements to your command.  If you're using a custom driver you will use a different `Scheduler` class.
```php
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Drivers\DateTime\Scheduler;
```
2. Extend `\Indatus\Dispatcher\Scheduling\ScheduledCommand`
3. Implement schedule():
```php
	/**
	 * When a command should run
	 *
	 * @param Scheduler $scheduler
	 *
	 * @return Scheduler
	 */
	public function schedule(Schedulable $scheduler)
	{
		return $scheduler;
    }
```

For details and examples on how to schedule, see the [DateTime Driver](#datetime).

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

> NOTE: Both `args()` and `opts()`, whichever is called first, will internally create a new `Schedulable` instance for you so you don't need to `App::make()`.

<a name="drivers" />
## Drivers

Drivers provide the ability to add additional context to your scheduling.  [Building custom drivers](#custom-drivers) is a great way to customize this context to your application's needs.

<a name="datetime" />
### DateTime (Default)

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

```php
	public function schedule(Schedulable $scheduler)
	{
        //the second and third Tuesday of every month at 12am
        return $scheduler->monthly()->week([2, 3])->daysOfTheWeek(Day::TUESDAY);
    }
```

<a name="custom-drivers" />
## Custom Drivers

Custom drivers allow you to provide application context within scheduling.  For example, an education-based application may contain scheduling methods like `inServiceDays()`, `springBreak()` and `christmasBreak()` where commands are run or don't run during those times.

Create a packagepath such as `\MyApp\ScheduleDriver\` and create two classes:

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
