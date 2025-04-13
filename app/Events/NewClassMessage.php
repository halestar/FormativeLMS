<?php

namespace App\Events;

use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewClassMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    /**
     * Create a new event instance.
     */
    public function __construct(public ClassMessage $message){}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        //find the recipients
        $student = $this->message->student;
        $recipients = [];
        //student
        if($student->person->id != $this->message->person_id)
            $recipients[] = $student->person;
        //class teachers
        $teachers = $this->message->session->teachers;
        foreach($teachers as $teacher)
            if($teacher->id != $this->message->person_id)
                $recipients[] = $teacher;
        //student parents
        foreach($student->person->parents as $parent)
            if($parent->id != $this->message->person_id)
                $recipients[] = $parent;

        $channels = [];
        foreach($recipients as $recipient)
            $channels[] = new PrivateChannel('people.' . $recipient->id);
        return $channels;
    }

    public function broadcastAs(): string
    {
        return 'newClassMessage';
    }
}
