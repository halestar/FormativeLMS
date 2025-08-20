<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Models\Utilities\WorkFile;
use Illuminate\Http\Request;

class StorageController extends Controller
{
	public static function middleware()
	{
		return ['auth'];
	}
	
	public function downloadWorkFile(Request $request, WorkFile $work_file)
	{
		//get the storage
		$storage = $work_file->storageInstance();
		return $storage->download($work_file);
	}
}
