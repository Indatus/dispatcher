**Dispatcher** allows you to schedule your artisan commands within your Laravel project, eliminating the need to touch the crontab when deploying.  It also allows commands to run per environment and keeps your scheduling logic where it should be, in your version control.

[![Latest Stable Version](https://poser.pugx.org/indatus/dispatcher/v/stable.png)](https://packagist.org/packages/indatus/dispatcher) [![Total Downloads](https://poser.pugx.org/indatus/dispatcher/downloads.png)](https://packagist.org/packages/indatus/dispatcher) [![Build Status](https://travis-ci.org/Indatus/dispatcher.png?branch=master)](https://travis-ci.org/Indatus/dispatcher) [![Coverage Status](https://coveralls.io/repos/Indatus/dispatcher/badge.png?branch=master)](https://coveralls.io/r/Indatus/dispatcher?branch=master)

<img align="left" height="300" src="https://s3-us-west-2.amazonaws.com/oss-avatars/dispatcher_round_readme.png">

```php
<?php

use Indatus\Dispatcher\ScheduledCommand;

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

## README Contents

* [Features](#features)
* [Installation](#installation)
* [Usage](#usage)
  * [Generating New Scheduled Commands](#new-commands)
  * [Scheduling Existing Commands](#scheduling-commands)
  * [Running Commands As Users](#commands-as-users)
  * [Environment-specific commands](#environment-commands)
* [Custom Schedule Drivers](#customer-drivers)
* [Roadmap](#roadmap)
* [FAQ](#faq)

<a name="features" />
## Features

 * Schedule artisan commands to run automatically
 * Scheduling is maintained within your version control system
 * Run commands as other users
 * Run commands in certain environments

<a name="installation" />
## Installation

You can install the library via [Composer](http://getcomposer.org) by adding the following line to the **require** block of your *composer.json* file:

````
"indatus/dispatcher": "dev-master"
````

Next run `composer update`.

Add this line to the providers array in your `app/config/app.php` file :

```php
        'Indatus\Dispatcher\ServiceProvider',
```

Add the following cron.  If you'd like for scheduled commands to be able to run as different users, be sure to add this to the root crontab.  Otherwise all commands run as the user whose crontab you've added this to.

```php
* * * * * php /path/to/artisan scheduled:run 1>> /dev/null 2>&
```

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

Use `php artisan scheduled:make` to generate a new scheduled command, the same way you would use artisan's `command:make`.

<a name="scheduling-commands" />
### Scheduling Existing Commands

Simply `extend \Indatus\Dispatcher\ScheduledCommand` and implement the `schedule()` method within your command.  This method is injected with a `Schedulable` interface provides some incredibly useful methods for scheduling your commands.

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
                Scheduler::$TUESDAY,
                Scheduler::$THURSDAY
            ])->hours(5)->minutes(3);
    }
```

<a name="commands-as-users" />
### Running Commands As Users

You may override `user()` to run a given artisan command as a specific user.  Ensure your `scheduled:run` artisan command is running as root.

```php
    public function user()
    {
        return 'backup';
    }
```

<a name="environment-commands" />
### Environment-specific commands

You may override `environment()` to ensure your command is only scheduled in specific environments.  It should provide a single environment or an array of environments.

```php
    public function environment()
    {
        return ['development','staging'];
    }
```

<a name="customer-drivers" />
## Custom Schedule Drivers

You can build your own drivers or extend a driver that's included.  Create a packagepath such as `\MyApp\ScheduleDriver\` and create two classes:

 * `Scheduler` that `implements Indatus\Dispatcher\Schedulable`
 * `ScheduleService` that `extends \Indatus\Dispatcher\Services\ScheduleService`

 Then update your driver configuration to reference the package in which these 2 classes are included (do not include a trailing slash):

```php
    'driver' => '\MyApp\ScheduleDriver'
```

<a name="roadmap" />
## Roadmap

 * Accommodate a single command with various parameter sets

<a name="faq" />
## FAQ

**Why do I see a RuntimeExceptionWhen I use `php artisan scheduled:run`?**

When running scheduled commands, exceptions from a command will appear as if they came from `scheduled:run`.  More than likely, it's one of your commands that is throwing the exception.

**I have commands that extend `ScheduledCommand` but why don't they appear in when I run `scheduled:summary`?**

Commands that are disabled will not appear here.  Check and be sure `isEnabled()` returns true on those commands.
