<?php

namespace App\Observers;

use App\Interfaces\Fileable;

class FileableObserver
{
	public function deleting(Fileable $fileable)
	{
		foreach($fileable->workFiles as $file)
			$file->delete();
	}
}
