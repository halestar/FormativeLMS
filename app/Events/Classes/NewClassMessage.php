<?php

namespace App\Events\Classes;

use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewClassMessage
{
	use Dispatchable, InteractsWithSockets, SerializesModels;
	
	
	/**
	 * Create a new event instance.
	 */
	public function __construct(public ClassMessage $message) {}

}
