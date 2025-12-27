<?php

namespace App\Http\Controllers\Storage;

use App\Http\Controllers\Controller;
use App\Models\Utilities\WorkFile;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class StorageController extends Controller
{
	public static function middleware()
	{
		return new Middleware('auth', only: ['downloadWorkFile']);
	}
	
	public function downloadWorkFile(Request $request, WorkFile $work_file)
	{
		Gate::authorize('view', $work_file);
		//get the storage
		return $work_file->lmsConnection->download($work_file);
	}

	public function downloadUnkn()
	{
		$headers =
			[
				'Content-Type: image/png',
				'Content-Disposition: inline',
			];
		return response()->download(public_path('images/unk.png'), 'unk.png', $headers);
	}

	public function downloadWorkFileThumb(Request $request, WorkFile $work_file)
	{
		Gate::authorize('view', $work_file);
		//first, can this file have a thumb? If not, send back the ukn.png data.
		if(!$work_file->canCreateThumb()) return $this->downloadUnkn();
		//does the thumb file exists yet? if not return unkn.
		if(!$work_file->hasThumbnail()) return $this->downloadUnkn();
		//else, download the thumb.
		return $work_file->lmsConnection->downloadThumb($work_file);
	}
}
