<?php

namespace App\Notifications\Substitutes;

use App\Models\Substitutes\Substitute;
use App\Models\Substitutes\SubstituteRequest;
use App\Notifications\LmsNotification;
use Illuminate\Support\Facades\Blade;

class AcceptedRequestSubstituteNotification extends LmsNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public SubstituteRequest $subRequest, public Substitute $sub)
    {
		parent::__construct();
    }

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['request_id'] = $this->subRequest->id;
		$data['substitute_id'] = $this->sub->person_id;
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
				'{!! $classes_table !!}' => __('emails.substitutes.new.request.admin.link'),
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
				'classes_table' => Blade::render('substitutes.access.classes-table',
					[
						'subRequest' => $this->subRequest,
						'sub' => $this->sub,
					]),
			];
	}

	public static function requiredTokens(): array
	{
		return [];
	}

	public static function fakeNotification(): static
	{
		return new AcceptedRequestSubstituteNotification(SubstituteRequest::inRandomOrder()->first(), Substitute::inRandomOrder()->first());
	}

	public function actionLink(): string|null
	{
		return null;
	}
}
