<?php

namespace App\Jobs;

use App\Models\Utilities\WorkFile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class OptimizeImageJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $workFileId){}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //first, we try to load the work file
	    $workFile = WorkFile::find($this->workFileId);
	    //because this is an image, there is a chance that the image was deleted already, which is why we make this check.
		if($workFile && $workFile->mimeType->is_img)
		{
			if($workFile->canCreateThumb() && !$workFile->hasThumbnail())
				$workFile->generateThumb();
        }
    }
}
