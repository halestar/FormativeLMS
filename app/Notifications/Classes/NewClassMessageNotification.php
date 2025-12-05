<?php

namespace App\Notifications\Classes;

use App\Models\SubjectMatter\Components\ClassMessage;
use App\Notifications\LmsNotification;

class NewClassMessageNotification extends LmsNotification
{
    public ClassMessage $classMessage;
	
	/**
	 * Create a new notification instance.
	 */
	public function __construct(ClassMessage $classMessage)
    {
        parent::__construct();
        $this->classMessage = $classMessage;
    }

    public function toArray(object $notifiable): array
    {
        $data = parent::toArray($notifiable);
        $data['session_id'] = $this->classMessage->session_id;
        $data['message_id'] = $this->classMessage->id;
        $data['student_id'] = $this->classMessage->student_id;
        return $data;
    }


    public static function availableTokens(): array
    {
        return
            [
                '{!! $class_name !!}' => __('emails.class.status.tokens.class_name'),
                '{!! $message !!}' => __('emails.class.messages.tokens.message'),
                '{!! $posted_by !!}' => __('emails.class.messages.tokens.posted.by'),
                '{!! $posted_on !!}' => __('emails.class.messages.tokens.posted.on'),
            ];
    }

    public function withTokens(): array
    {
        return
            [
                'class_name' => $this->classMessage->session->name_with_schedule,
                'message' => $this->classMessage->message,
                'posted_by' => $this->classMessage->postedBy->name,
                'posted_on' => $this->classMessage->created_at->format('m/d/Y H:i A'),
            ];
    }

    public static function requiredTokens(): array
    {
        return [];
    }

    public static function fakeNotification(): static
    {
        $msg = ClassMessage::inRandomOrder()->first();
        return new NewClassMessageNotification($msg);
    }

    public function broadcastAs(): string
    {
        return 'classMessage';
    }

    public function actionLink(): string|null
    {
        return route('subjects.school.classes.messages', ['notification_id' => $this->id]);
    }

    public function databaseType(object $notifiable): string
    {
        return 'class-message';
    }
}
