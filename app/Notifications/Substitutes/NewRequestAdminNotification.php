<?php

namespace App\Notifications\Substitutes;

use App\Models\Substitutes\SubstituteRequest;
use App\Notifications\LmsNotification;
use Illuminate\Support\Collection;

class NewRequestAdminNotification extends LmsNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public SubstituteRequest $subRequest, public Collection $subs)
    {
		parent::__construct();
    }

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['request_id'] = $this->subRequest->id;
		$data['subs'] = $this->subs->pluck('id')->toArray();
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
				'{!! $subs_contacted !!}' => __('emails.substitutes.new.request.admin.subs_contacted'),
				'{!! $link !!}' => __('emails.substitutes.new.request.admin.link'),
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
				'link' => route('features.substitutes.show', $this->subRequest),
			];
	}

	public static function requiredTokens(): array
	{
		return [];
	}

	public static function fakeNotification(): static
	{
		return new NewRequestAdminNotification(SubstituteRequest::inRandomOrder()->first(), new Collection);
	}

	public function actionLink(): string|null
	{
		return route('features.substitutes.show', $this->subRequest);
	}
}
