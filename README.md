# Laravel Command Scheduler

Quit editing the crontab when you deploy and schedule your artisan commands within your project.

## Features

 * Schedule artisan commands to run automatically
 * Scheduling is maintained within your version control system
 * Run commands as other users

## Roadmap

 * Accomidate a single command with various parameter sets

## Installation

 [ADD INSTRUCTIONS FOR COMPOSER INSTALL]

Add this line to the providers array in your `app/config/app.php` file :

```php
        'Indatus\CommandScheduler\CommandSchedulerServiceProvider',
```

Add the following cron.  If you'd like for scheduled commands to be able to run as different users, be sure to add this to the root crontab.  Otherwise all commands run as the user whose crontab you've added this to.

```
* * * * * php /path/to/artisan scheduled:run 1>> /dev/null 2>&
```

### Scheduling Existing Commands

Simply `extend \Indatus\CommandScheduler\ScheduledCommand` and setup the `schedule()` method (see Scheduling) within your command.

## Usage
```
scheduled
  scheduled:make              Create a new scheduled artisan command
  scheduled:run               Run scheduled commands
  scheduled:summary           View a summary of all scheduled artisan commands
```

If commands are not visible via `php artisan` then they cannot be scheduled

### Generating New Scheduled Commands

Use `php artisan scheduled:make` to generate a new scheduled command, the same way you would use artisan's `command:make`.

## Scheduling

The `Schedulable` interface provides some incredibly useful methods for scheduling your cron commands.

```
//every day at 4:17am
return $scheduler->daily()->hours(4)->minutes(17);
```


```
//every Tuesday/Thursday at 5:03am
return $scheduler->daysOfTheWeek([Scheduler::$TUESDAY, Scheduler::$THURSDAY])->hours(5)->minutes(3);
```

## Extending

You can build your own drivers or extend a driver that's included.  Create a packagepath such as `\MyApp\ScheduleDriver\` and create two classes:

 * `Scheduler` that `implements Indatus\CommandScheduler\Schedulable`
 * `ScheduleService` that `extends \Indatus\CommandScheduler\Services\ScheduleService`

 Then update your driver configuration to reference the package in which these 2 classes are included (do not include a trailing slash):

```
    'driver' => '\MyApp\ScheduleDriver'
```

## FAQ

**Why do I see a RuntimeExceptionWhen I use `php artisan scheduled:run`?**

When running scheduled commands, exceptions from a command will appear as if they came from `scheduled:run`.  More than likely, it's one of your commands that is throwing the exception.

**I have commands that extend `ScheduledCommand` but why don't they appear in when I run `scheduled:summary`?**

Commands that are disabled will not appear here.  Check and be sure `isEnabled()` returns true on those commands.