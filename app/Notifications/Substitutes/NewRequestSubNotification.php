<?php

namespace App\Notifications\Substitutes;

use App\Models\Substitutes\SubstituteRequest;
use App\Notifications\LmsNotification;

class NewRequestSubNotification extends LmsNotification
{

    /**
     * Create a new notification instance.
     */
    public function __construct(public SubstituteRequest $subRequest, public string $link)
    {
	    parent::__construct();
    }

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['request_id'] = $this->subRequest->id;
		$data['link'] = $this->link;
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
				'{!! $link !!}' => __('emails.substitutes.new.request.link'),
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
				'link' => '<a href="' . $this->link . '">' . $this->link . '</a>',
			];
	}

	public static function requiredTokens(): array
	{
		return ['{!! $link !!}'];
	}

	public static function fakeNotification(): static
	{
		return new NewRequestSubNotification(SubstituteRequest::inRandomOrder()->first(), "http://fake.url");
	}

	public function actionLink(): string|null
	{
		return $this->link;
	}
}
