<?php

namespace Database\Seeders;

use App\Models\Utilities\MimeType;
use Illuminate\Database\Seeder;

class MimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
	    MimeType::create(
		    [
			    'mime' => 'image/png',
			    'extension' => '.png',
			    'icon' => '<i class="bi bi-filetype-png"></i>',
			    'is_img' => true,
		    ]);
        MimeType::create(
			[
				'mime' => 'image/jpeg',
				'extension' => '.jpg,.jpeg',
				'icon' => '<i class="bi bi-filetype-jpg"></i>',
				'is_img' => true,
			]);
	    MimeType::create(
		    [
			    'mime' => 'image/gif',
			    'extension' => '.gif',
			    'icon' => '<i class="bi bi-filetype-gif"></i>',
			    'is_img' => true,
		    ]);
	    MimeType::create(
		    [
			    'mime' => 'application/pdf',
			    'extension' => '.pdf',
			    'icon' => '<i class="bi bi-file-pdf"></i>',
			    'is_img' => false,
		    ]);
    }
}
