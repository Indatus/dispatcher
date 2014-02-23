Laravel Command Scheduler

## Features

 * Schedule artisan commands to run automatically
 * Scheduling is maintained within your version control system
 * Run commands as other users

## Pitfalls

 * No Rcron driver yet

## Installation

Add this line to the providers array in your `app/config/app.php` file :

```php
        'Indatus\CommandScheduler\CommandSchedulerServiceProvider',
```

Add the following cron

```
* * * * * php /path/to/artisan 1>> /dev/null 2>&
```

## Usage

If commands are not visible via `php artisan` then they cannot be scheduled

#### Generating New Scheduled Commands

Use `php artisan scheduled:make` to generate a new scheduled command, the same way you would use artisan's `command:make`.

#### Scheduling Existing Commands

All existing commands should `extend \Indatus\CommandScheduler\ScheduledCommand`.

#### Artisan Commands
```
scheduled
  scheduled:make              Create a new scheduled artisan command
  scheduled:run               Run scheduled commands
  scheduled:summary           View a summary of all scheduled artisan commands