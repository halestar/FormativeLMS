<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Process;

class DatabaseDumpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		$cmd = 'mysql -u ' . env('DB_USERNAME') . ' -p \'' . env('DB_PASSWORD') . '\' ' .
	        env('DB_DATABASE_TESTING') . ' < ' . storage_path('logs/fablms.sql');
        $process = Process::run($cmd);
		if($process->failed())
			throw new \Exception($process->errorOutput());
    }
}
