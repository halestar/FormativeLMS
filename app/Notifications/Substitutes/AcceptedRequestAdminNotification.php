<?php

namespace App\Notifications\Substitutes;

use App\Classes\Settings\ServerSettings;
use App\Models\Substitutes\CampusRequest;
use App\Models\Substitutes\SubstituteCampusRequest;
use App\Models\Substitutes\SubstituteRequest;
use App\Notifications\LmsNotification;
use App\Notifications\SystemNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Collection;

class AcceptedRequestAdminNotification extends LmsNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public SubstituteCampusRequest $subCampusRequest)
    {
		parent::__construct();
    }

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['request_id'] = $this->subCampusRequest->id;
		return $data;
	}

	public static function availableTokens(): array
	{
		return
			[
				'{!! $substitute_name !!}' => __('emails.substitutes.accepted.request.admin.substitute.name'),
				'{!! $substitute_email !!}' => __('emails.substitutes.accepted.request.admin.substitute.email'),
				'{!! $substitute_phone !!}' => __('emails.substitutes.accepted.request.admin.substitute.phone'),
				'{!! $teacher_name !!}' => __('emails.substitutes.new.request.teacher'),
				'{!! $coverage_date !!}' => __('emails.substitutes.new.request.date'),
				'{!! $coverage_start !!}' => __('emails.substitutes.new.request.start'),
				'{!! $coverage_end !!}' => __('emails.substitutes.new.request.end'),
				'{!! $link !!}' => __('emails.substitutes.new.request.admin.link'),
			];
	}

	public function withTokens(): array
	{
		return
			[
				'substitute_name' => $this->subCampusRequest->subRequest->substitute->name,
				'substitute_email' => $this->subCampusRequest->subRequest->substitute->email,
				'substitute_phone' => $this->subCampusRequest->subRequest->substitute->phone->prettyPhone,
				'teacher_name' => $this->subCampusRequest->subRequest->requester_name,
				'coverage_date' => $this->subCampusRequest->subRequest->requested_for->format('m/d/Y'),
				'coverage_start' => $this->subCampusRequest->subRequest->startTime()->format('g:i A'),
				'coverage_end' => $this->subCampusRequest->subRequest->endTime()->format('g:i A'),
				'link' => route('features.substitutes.show', $this->subCampusRequest->subRequest),
			];
	}

	public static function requiredTokens(): array
	{
		return [];
	}

	public static function fakeNotification(): static
	{
		return new AcceptedRequestAdminNotification(SubstituteCampusRequest::inRandomOrder()->first());
	}

	public function actionLink(): string|null
	{
		return route('features.substitutes.show', $this->subCampusRequest->subRequest);
	}
}
