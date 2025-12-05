<?php

namespace App\Notifications\Classes;

use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassStatus;
use App\Models\Utilities\SchoolMessage;
use App\Notifications\LmsNotification;

class NewClassStatusNotification extends LmsNotification
{

    public ClassStatus $classStatus;
    public function __construct(ClassStatus $classStatus)
    {
        parent::__construct();
        $this->classStatus = $classStatus;
    }
    public static function availableTokens(): array
    {
        return
            [
                '{!! $class_name !!}' => __('emails.class.status.tokens.class_name'),
                '{!! $status !!}' => __('emails.class.status.tokens.status'),
                '{!! $teachers !!}' => __('emails.class.status.tokens.teachers'),
                '{!! $poster_name !!}' => __('emails.class.status.tokens.poster_name'),
                '{!! $expiry_date !!}' => __('emails.class.status.tokens.expiry'),
            ];
    }

    public function withTokens(): array
    {
        return
        [
            'class_name' => $this->classStatus->classSession->name_with_schedule,
            'status' => $this->classStatus->announcement,
            'teachers' => $this->classStatus->classSession->teachersString(),
            'poster_name' => $this->classStatus->postedBy->name,
            'expiry_date' => $this->classStatus->expiry->format('m/d'),
        ];
    }

    public static function requiredTokens(): array
    {
        return ['{!! $class_name !!}'];
    }

    public static function fakeNotification(): static
    {
        $status = ClassStatus::where('className', ClassStatus::class)->inRandomOrder()->first();
        return new NewClassStatusNotification($status);
    }

    public function actionLink(): string|null
    {
        return route('subjects.school.classes.show', $this->classStatus->classSession->id);
    }

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['session_id'] = $this->classStatus->session_id;
		return $data;
	}
}