<?php

namespace App\Events\Learning;

use App\Models\SubjectMatter\Learning\LearningDemonstration;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DemonstrationUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * Create a new event instance.
	 */
	public function __construct(public LearningDemonstration $demonstration){}
}
