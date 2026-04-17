<?php

namespace App\Notifications\Substitutes;

use App\Models\People\Person;
use App\Models\People\Phone;
use App\Models\Substitutes\Substitute;
use App\Notifications\LmsNotification;

class NewSubstituteWelcomeNotification extends LmsNotification
{

    public Substitute $substitute;

    /**
     * Create a new message instance.
     */
    public function __construct(Person $substitute)
    {
	    parent::__construct();
        $this->substitute = $substitute->substituteProfile;
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
			];
	}

	public static function requiredTokens(): array
	{
		return [];
	}

	public static function fakeNotification(): static
	{
		return new NewSubstituteWelcomeNotification(Substitute::inRandomOrder()->first()->person);
	}

	public function actionLink(): string|null
	{
		return null;
	}
}
