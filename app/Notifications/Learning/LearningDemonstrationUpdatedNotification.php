<?php

namespace App\Notifications\Learning;

use App\Models\SubjectMatter\Learning\LearningDemonstrationOpportunity;
use App\Notifications\LmsNotification;

class LearningDemonstrationUpdatedNotification extends LmsNotification
{
	public LearningDemonstrationOpportunity $opportunity;

    /**
     * Create a new notification instance.
     */
    public function __construct(LearningDemonstrationOpportunity $opportunity)
    {
		parent::__construct();
        $this->opportunity = $opportunity;
    }

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['opportunity_id'] = $this->opportunity->id;
		return $data;
	}

	public static function availableTokens(): array
	{
		return
			[
				'{!! $class_name !!}' => __('emails.class.status.tokens.class_name'),
				'{!! $demonstration_name !!}' => __('learning.demonstrations.name'),
				'{!! $demonstration_abbr !!}' => __('learning.demonstrations.abbr'),
				'{!! $demonstration_description !!}' => __('learning.demonstrations.demonstration'),
				'{!! $demonstration_skills !!}' => __('learning.demonstrations.skills'),
				'{!! $posted_on !!}' => __('emails.class.messages.tokens.posted.on'),
			];
	}

	public function withTokens(): array
	{
		return
			[
				'class_name' => $this->opportunity->demonstrationSession->classSession->name_with_schedule,
				'demonstration_name' => $this->opportunity->demonstration->name,
				'demonstration_abbr' => $this->opportunity->demonstration->abbr,
				'demonstration_description' => $this->opportunity->demonstration->demonstration,
				'demonstration_skills' => $this->opportunity->skills->implode(fn($skill) => $skill->prettyName(), ', '),
				'posted_on' => $this->opportunity->posted_on->format('m/d/Y H:i A'),
			];
	}

	public static function requiredTokens(): array
	{
		return ['{!! $class_name !!}'];
	}

	public static function fakeNotification(): static
	{
		$msg = LearningDemonstrationOpportunity::inRandomOrder()->first();
		return new LearningDemonstrationUpdatedNotification($msg);
	}

	public function actionLink(): string|null
	{
		return null;
	}
}
