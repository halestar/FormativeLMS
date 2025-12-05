<?php

namespace App\Notifications\Classes;

use App\Models\SubjectMatter\Components\ClassAnnouncement;
use App\Models\SubjectMatter\Components\ClassCommunicationObject;
use App\Notifications\LmsNotification;

class ClassAlert extends LmsNotification
{
	public ClassCommunicationObject $classActivity;
	public string $action;
	/**
	 * Create a new notification instance.
	 */
	public function __construct(ClassCommunicationObject $classActivity, string $action)
	{
		parent::__construct();
        $this->classActivity = $classActivity;
        $this->action = $action;
	}

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['session_id'] = $this->classActivity->session_id;
		return $data;
	}

    public static function availableTokens(): array
    {
        return
            [
                '{!! $class_name !!}' => __('emails.class.status.tokens.class_name'),
                '{!! $activity_action !!}' => __('class.activity.tokens.activity_action'),
                '{!! $posted_by !!}' => __('emails.class.messages.tokens.posted.by'),
                '{!! $posted_on !!}' => __('emails.class.messages.tokens.posted.on'),
            ];
    }

    public function withTokens(): array
    {
        return
            [
                'class_name' => $this->classActivity->classSession->name_with_schedule,
                'activity_action' => $this->action,
                'posted_by' => $this->classActivity->postedBy->name,
                'posted_on' => $this->classActivity->created_at->format('m/d/Y H:i A'),
            ];
    }

    public static function requiredTokens(): array
    {
        return ['{!! $class_name !!}', '{!! $activity_action !!}'];
    }

    public static function fakeNotification(): static
    {
        $msg = ClassAnnouncement::inRandomOrder()->first();
        return new ClassAlert($msg, __('subjects.school.widgets.class-announcements'));
    }

    public function actionLink(): string|null
    {
        return route('subjects.school.classes.show', $this->classActivity->classSession->id);
    }
}
