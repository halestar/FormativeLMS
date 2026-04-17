<?php

namespace App\Notifications\Substitutes;

use App\Models\Substitutes\SubstituteRequest;
use App\Notifications\LmsNotification;

class NewSubRequestTeacherNotification extends LmsNotification
{

    /**
     * Create a new message instance.
     */
    public function __construct(public SubstituteRequest $subRequest)
    {
	    parent::__construct();
    }

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['request_id'] = $this->subRequest->id;
		return $data;
	}

	public static function availableTokens(): array
	{
		return
			[
				'{!! $teacher_name !!}' => __('emails.substitutes.new.request.teacher'),
				'{!! $coverage_date !!}' => __('emails.substitutes.new.request.date'),
				'{!! $coverage_start !!}' => __('emails.substitutes.new.request.start'),
				'{!! $coverage_end !!}' => __('emails.substitutes.new.request.end'),
			];
	}

	public function withTokens(): array
	{
		return
			[
				'teacher_name' => $this->subRequest->requester_name,
				'coverage_date' => $this->subRequest->requested_for->format('m/d/Y'),
				'coverage_start' => $this->subRequest->startTime()->format('g:i A'),
				'coverage_end' => $this->subRequest->endTime()->format('g:i A'),
			];
	}

	public static function requiredTokens(): array
	{
		return [];
	}

	public static function fakeNotification(): static
	{
		return new NewSubRequestTeacherNotification(SubstituteRequest::inRandomOrder()->first());
	}

	public function actionLink(): string|null
	{
		return null;
	}

}
