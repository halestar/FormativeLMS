<?php

namespace App\Notifications\Learning;

use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Learning\LearningDemonstrationOpportunity;
use App\Notifications\LmsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LearningDemonstrationDeletedNotification extends LmsNotification
{
    public string $demonstrationName;
	public ClassSession $classSession;

    /**
     * Create a new notification instance.
     */
    public function __construct(ClassSession $classSession, string $demonstrationName)
    {
	    parent::__construct();
        $this->classSession = $classSession;
        $this->demonstrationName = $demonstrationName;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
	    $data = parent::toArray($notifiable);
	    $data['session_id'] = $this->classSession->id;
		$data['demonstration_name'] = $this->demonstrationName;
	    return $data;
    }

	public static function availableTokens(): array
	{
		return
			[
				'{!! $class_name !!}' => __('emails.class.status.tokens.class_name'),
				'{!! $demonstration_name !!}' => __('learning.demonstrations.name'),
			];
	}

	public function withTokens(): array
	{
		return
			[
				'class_name' => $this->classSession->name_with_schedule,
				'demonstration_name' => $this->demonstrationName,
			];
	}

	public static function requiredTokens(): array
	{
		return ['{!! $class_name !!}', '{!! $demonstration_name !!}'];
	}

	public static function fakeNotification(): static
	{
		$session = ClassSession::inRandomOrder()->first();
		$name = "Random Learning Demonstration";
		return new LearningDemonstrationDeletedNotification($session, $name);
	}

	public function actionLink(): string|null
	{
		return route('subjects.school.classes.show', $this->classSession);
	}

}
