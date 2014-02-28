Laravel Command Scheduler

## Features

 * Schedule artisan commands to run automatically
 * Scheduling is maintained within your version control system
 * Run commands as other users

## Roadmap

 * Accomidate scheduling parameterized commands
 * Handle exceptions individually, so one exception in a command doesn't stop all scheduled commands from running

## Installation

Add this line to the providers array in your `app/config/app.php` file :

```php
        'Indatus\CommandScheduler\CommandSchedulerServiceProvider',
```

Add the following cron.  Run it as root if you'd like for scheduled commands to be able to run as different users.

```
* * * * * php /path/to/artisan scheduled:run 1>> /dev/null 2>&
```

#### Scheduling Existing Commands

Simply `extend \Indatus\CommandScheduler\ScheduledCommand` and setup the `schedule()` method within your command.

## Usage
```
scheduled
  scheduled:make              Create a new scheduled artisan command
  scheduled:run               Run scheduled commands
  scheduled:summary           View a summary of all scheduled artisan commands
```

If commands are not visible via `php artisan` then they cannot be scheduled

#### Generating New Scheduled Commands

Use `php artisan scheduled:make` to generate a new scheduled command, the same way you would use artisan's `command:make`.

## FAQ

**Why do I see a RuntimeExceptionWhen I use `php artisan scheduled:run`?**

When running scheduled commands, exceptions from a command will appear as if they came from `scheduled:run`.  More than likely, it's one of your commands that is throwing the exception.