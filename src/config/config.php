<?php return array(
    /**
     * The schedule driver to use:
     *
     *   cron               The most common method of scheduling commands.  Uses the basic cron packaged
     *                      included with any linux environment.
     *
     *   [packagePath]      Create your own driver by providing a classpath to a package that contains
     *                      Scheduler and SchedulerService classes.
     *
     */
    'driver' => 'cron'
);