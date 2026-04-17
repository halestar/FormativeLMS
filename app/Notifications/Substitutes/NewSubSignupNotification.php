<?php

namespace App\Notifications\Substitutes;

use App\Models\People\Phone;
use App\Models\Substitutes\Substitute;
use App\Notifications\LmsNotification;

class NewSubSignupNotification extends LmsNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(public Substitute $substitute)
    {
	    parent::__construct();
    }

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['person'] = $this->substitute->school_id;
		return $data;
	}

	public static function availableTokens(): array
	{
		return
			[
				'{!! $recipient !!}' => __('emails.password.reset.recipient'),
				'{!! $recipient_email !!}' => __('emails.password.reset.recipient_email'),
				'{!! $email_status !!}' => __('emails.substitutes.welcome.email_status'),
				'{!! $sms_status !!}' => __('emails.substitutes.welcome.sms_status'),
				'{!! $campuses !!}' => __('emails.substitutes.welcome.campuses'),
				'{!! $link_to_profile !!}' => __('emails.substitutes.welcome.link_to_profile'),
			];
	}

	public function withTokens(): array
	{
		return
			[
				'recipient' => $this->substitute->name,
				'recipient_email' => $this->substitute->email,
				'email_status' => $this->substitute->email_confirmed ? 'Enabled' : 'Not enabled',
				'sms_status' => ($this->substitute->sms_confirmed && $this->substitute->phone instanceof Phone)?
					"Enabled (" . $this->substitute->phone->prettyPhone . ")": "Not enabled",
				'campuses' => $this->substitute->campuses->isNotEmpty()?
					$this->substitute->campuses->pluck('name')->implode(', '): 'None assigned',
				'link_to_profile' => route('features.substitutes.pool.show', $this->substitute),
			];
	}

	public static function requiredTokens(): array
	{
		return [];
	}

	public static function fakeNotification(): static
	{
		return new NewSubSignupNotification(Substitute::inRandomOrder()->first());
	}

	public function actionLink(): string|null
	{
		return route('features.substitutes.pool.show', $this->substitute);
	}
}
