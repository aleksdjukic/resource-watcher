<?php namespace JasonLewis\Watcher;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class WatchCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'watch';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = "Wach a file for changes";

	/**
	 * Create a new command creator command.
	 *
	 * @param  JasonLewis\Watcher\Watcher  $watcher
	 * @return void
	 */
	public function __construct(Watcher $watcher)
	{
		parent::__construct();

		$this->watcher = $watcher;
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
		$path = $this->laravel['path.base'].'/'.$this->input->getArgument('file');

		$output = $this->output;

		$this->watcher->watch($path, function($resource, $event) use ($output)
		{
			$path = $resource->getPath();

			switch ($event->getCode())
			{
				case Event::RESOURCE_DELETED:
					$output->writeln("<comment>{$path} has been deleted.</comment>");
					break;
				case Event::RESOURCE_MODIFIED:
					$output->writeln("<comment>{$path} has been modified.</comment>");
					break;
				case Event::RESOURCE_CREATED:
					$output->writeln("<comment>{$path} has been created.</comment>");
					break;
			}
		});

		$realPath = realpath($path);

		if ($this->laravel['files']->isDirectory($path))
		{
			$this->info("Watching for changes in {$realPath}");
		}
		else
		{
			$this->info("Watching for changes to {$realPath}");
		}

		$this->watcher->startWatch(1000000);
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			array('file', InputArgument::REQUIRED, 'The file to watch'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array();
	}

}