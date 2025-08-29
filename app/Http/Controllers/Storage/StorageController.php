<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Models\Utilities\WorkFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;

class StorageController extends Controller
{
	public static function middleware()
	{
		return new Middleware('auth', only: ['downloadWorkFile']);
	}
	
	public function downloadWorkFile(Request $request, WorkFile $work_file)
	{
		//get the storage
		$storage = $work_file->storageInstance();
		return $storage->download($work_file);
	}
	
	public function downloadPublicWorkFile(Request $request, WorkFile $work_file)
	{
		if(!$work_file->public)
			abort(404);
		//get the storage
		$storage = $work_file->storageInstance();
		return $storage->download($work_file);
	}
}
