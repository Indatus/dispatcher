<?php

namespace Indatus\LaravelCommandScheduler;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Crontab extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'crontab:name';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{

	}

}
