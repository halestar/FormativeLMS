<?php

namespace App\Console\Commands;

use App\Models\Utilities\WorkFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearLocalDocuments extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fablms:clear-local-documents';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clears the local documents and storage folder';
	
	protected $keepDirs =
		[
			'app',
			'framework',
			'logs',
		];
	
	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$this->info('Removing all local files');
		//delete all the work files.
		$allDirs = File::directories(storage_path('/'));
		foreach($allDirs as $dir)
		{
			$dirName = basename($dir);
			if(!in_array($dirName, $this->keepDirs))
			{
				$this->info('Deleting Folder ' . $dir);
				File::deleteDirectory($dir);
			}
		}
		//clear all the work files.
		WorkFile::truncate();
	}
}
