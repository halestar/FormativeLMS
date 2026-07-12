<?php

namespace App\Console\Commands;

use App\Models\Integrations\Connections\AuthConnection;
use App\Models\People\Person;
use DB;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;

class CreateTestDb extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fablms:create-test-db';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Creates a test database using a fresh new migration and the selected seeding data.';

	/**
	 * Execute the console command.
	 */
	public function handle()
	{
		if (!config('lms.testing_db', false) || !config('database.testing'))
		{
			$this->error('The testing database is not enabled.');
			return;
		}
		$targetConnection = config('database.testing');
		config(['database.default' => $targetConnection]);
		DB::purge($targetConnection);
		DB::reconnect($targetConnection);
		$this->call('optimize:clear');
		$this->call('migrate:fresh',
			[
				'--database' => config('database.testing'),
			]);
		$this->call('db:seed',
			[
				'--database' => config('database.testing'),
				'--class'    => 'SimpleSeeder',
			]);
		//the testing requires some testing accounts to be created and logged in
		$accounts =
			[
				'fablms@kalinec.net',
				'staff@kalinec.net',
				'faculty@kalinec.net',
				'student@kalinec.net',
				'parent@kalinec.net',
			];

		$sessionHandler = new ArraySessionHandler(10);
		$session = new Store('session_name', $sessionHandler);
		$fakeRequest = Request::create('/home');
		$fakeRequest->setLaravelSession($session);
		app()->instance('request', $fakeRequest);
		foreach ($accounts as $account)
		{
			$person = Person::where('email', $account)->first();
			if ($person)
			{
				AuthConnection::completeLogin($person);
			}
		}
		$this->info("Test database created and ready. Run tests with php artisan test --testsuite=Feature");
	}
}
