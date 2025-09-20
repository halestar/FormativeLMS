<?php

namespace App\Console\Commands;

use App\Classes\Integrators\IntegrationsManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class RegisterIntegrator extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fablms:register-integrator {className : The name of the main Integrator class to register} {--f|force : This flag will force re-registration}';
	
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This command will register (or re-register) the specified Integrator class into the system and register all the services.';
	
	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		$className = str_replace(['/', "\\"], '\\', $this->argument('className'));
		$this->info('Registering integrator: ' . $className);
		$manager = App::make(IntegrationsManager::class);
		if($manager->registerIntegrator($className, $this->option('force')))
			$this->info('Integrator registered successfully.');
		else
			$this->error('There was an error registering the integrator.');
	}
}
