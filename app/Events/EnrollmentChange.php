<?php

namespace App\Events;

use App\Models\SubjectMatter\SchoolClass;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EnrollmentChange implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public const ENROLL = 1;
    public const UNENROLL = 2;
    /**
     * Create a new event instance.
     */
    public function __construct(public int $enrollmentType, public SchoolClass $schoolClass, public array $studentIds){}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('enrollment');
    }

    public function broadcastAs(): string
    {
        return 'enrollmentChange';
    }
}
