<?php

namespace Database\Seeders;

use App\Classes\Settings\EmailSetting;
use Illuminate\Database\Seeder;

class SchoolEmailSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run(): void
	{
		foreach(EmailSetting::allEmails() as $emailType)
			$emailType::createSettings();
	}
}
