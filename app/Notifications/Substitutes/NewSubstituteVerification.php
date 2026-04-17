<?php

namespace App\Notifications\Substitutes;

use App\Models\People\Person;
use App\Notifications\LmsNotification;
use Illuminate\Support\Facades\Auth;

class NewSubstituteVerification extends LmsNotification
{

    public Person $substitute;

    public string $url;

    /**
     * Create a new message instance.
     */
    public function __construct(Person $substitute)
    {
	    parent::__construct();
        $this->substitute = $substitute;
        $this->url = $this->substitute->substituteProfile->createAccessUrl();
    }

	public function toArray(object $notifiable): array
	{
		$data = parent::toArray($notifiable);
		$data['person'] = $this->substitute->school_id;
		$data['url'] = $this->url;
		return $data;
	}

	public static function availableTokens(): array
	{
		return
			[
				'{!! $recipient !!}' => __('emails.password.reset.recipient'),
				'{!! $recipient_email !!}' => __('emails.password.reset.recipient_email'),
				'{!! $url !!}' => __('emails.substitutes.verification.new.url'),
			];
	}

	public function withTokens(): array
	{
		return
			[
				'recipient' => $this->substitute->name,
				'recipient_email' => $this->substitute->system_email,
				'url' => '<a href="' . $this->url . '">' . $this->url . '</a>',
			];
	}

	public static function requiredTokens(): array
	{
		return ['{!! $url !!}'];
	}

	public static function fakeNotification(): static
	{
		return new NewSubstituteVerification(Auth::user());
	}

	public function actionLink(): string|null
	{
		return $this->url;
	}
}
