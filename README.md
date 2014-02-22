Laravel Command Scheduler


## Installation

Add this line to the providers array in your `app/config/app.php` file :

```php
        'Indatus\CommandScheduler\CommandSchedulerServiceProvider',
```

## Usage

### Generating New Scheduled Commands

Use `php artisan scheduled:make` to generate a new scheduled command, the same way you would use artisan's `command:make`.

### Scheduling Existing Commands

All existing commands should either `extend \Indatus\CommandScheduler\Schedulable` or `implement \Indatus\CommandScheduler\ScheduledCommandInterface`.

### Artisan Commands
```
scheduled
  scheduled:make              Create a new scheduled artisan command
  scheduled:summary           View a summary of all scheduled artisan commands
``