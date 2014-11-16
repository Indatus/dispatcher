<?php namespace Indatus\Dispatcher\Drivers\DateTime;

/**
 * This file is part of Dispatcher
 *
 * (c) Ben Kuhl <bkuhl@indatus.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App;
use Exception;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommandInterface;

class ScheduleService extends \Indatus\Dispatcher\Services\ScheduleService
{
    /** @var \Indatus\Dispatcher\Table */
    protected $table;

    /**
     * Determine if a schedule is due to be run.
     *
     * @param \Indatus\Dispatcher\Scheduling\Schedulable $scheduler
     *
     * @return bool
     */
    public function isDue(Schedulable $scheduler)
    {
        /** @var \Indatus\Dispatcher\Drivers\DateTime\ScheduleInterpreter $interpreter */
        $interpreter = App::make('Indatus\Dispatcher\Drivers\DateTime\ScheduleInterpreter', [$scheduler]);

        /** @var \Illuminate\Contracts\Logging\Log $logger */
        $logger = App::make('Illuminate\Contracts\Logging\Log');

        try {
            return $interpreter->isDue();
        } catch (Exception $e) {
            $logger->error($e);
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function printSummary()
    {
        $this->table = App::make('Indatus\Dispatcher\Table');

        $headers = [
            'Environment(s)',
            'Name',
            'Args/Opts',
            'Month',
            'Week',
            'Day of Month',
            'Day of Week',
            'Hour',
            'Minute',
            'Run as',
        ];

        $this->table->setHeaders($headers);
        /** @var \Indatus\Dispatcher\Scheduling\ScheduledCommandInterface $command */
        foreach ($this->getScheduledCommands() as $command) {
            /** @var \Indatus\Dispatcher\Scheduling\Schedulable $scheduler */
            $scheduler = $command->schedule(App::make('Indatus\Dispatcher\Scheduling\Schedulable'));

            //if there's only one schedule, print just the command
            if (!is_array($scheduler)) {
                $this->printCommand($command, $scheduler);
            } else {
                if ($this->printCommandLabel($command)) {
                    /** @var \Indatus\Dispatcher\Scheduling\Schedulable $schedule */
                    foreach ($scheduler as $schedule) {
                        $this->printSchedule($command, $schedule);
                    }
                }
            }
        }

        $this->table->display();
    }

    protected function printCommand(ScheduledCommandInterface $command, Schedulable $scheduler)
    {
        $this->table->addRow([
                is_array($command->environment()) ? implode(',', $command->environment()) : $command->environment(),
                $command->getName(),
                '',
                $scheduler->getScheduleMonth(),
                $scheduler->getScheduleWeek(),
                $scheduler->getScheduleDayOfMonth(),
                $scheduler->getScheduleDayOfWeek(),
                $scheduler->getScheduleHour(),
                $scheduler->getScheduleMinute(),
                $command->user(),
            ]);
    }

    protected function printCommandLabel(ScheduledCommandInterface $command)
    {
        $this->table->addRow([
                is_array($command->environment()) ? implode(',', $command->environment()) : $command->environment(),
                $command->getName(),
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $command->user(),
            ]);

        return true;
    }

    protected function printSchedule(ScheduledCommandInterface $command, Schedulable $scheduler)
    {
        $commandService = App::make('Indatus\Dispatcher\Services\CommandService');

        $arguments = $commandService->prepareArguments($scheduler->getArguments());
        $options = $commandService->prepareOptions($scheduler->getOptions());

        $this->table->addRow([
                '',
                '',
                trim($arguments).' '.$options,
                $scheduler->getScheduleMonth(),
                $scheduler->getScheduleWeek(),
                $scheduler->getScheduleDayOfMonth(),
                $scheduler->getScheduleDayOfWeek(),
                $scheduler->getScheduleHour(),
                $scheduler->getScheduleMinute(),
                '',
            ]);
    }
}
