<?php

namespace App\Events\Learning;

use App\Models\SubjectMatter\ClassSession;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DemonstrationDeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public ClassSession $classSession, public array $students, public string $demonstrationName){}

}
