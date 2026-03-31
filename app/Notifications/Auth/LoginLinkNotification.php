<?php

namespace App\Notifications\Auth;

use App\Models\People\Person;
use App\Models\Utilities\SchoolMessage;
use App\Notifications\LmsNotification;
use Auth;

class LoginLinkNotification extends LmsNotification
{
	/**
     * Create a new notification instance.
     */
    public function __construct(protected Person $recipient, protected string $url)
    {
        parent::__construct();
    }

    public static function availableTokens(): array
    {
        return
            [
                '{!! $recipient !!}' => __('emails.password.reset.recipient'),
                '{!! $recipient_email !!}' => __('emails.password.reset.recipient_email'),
                '{!! $url !!}' => __('emails.login.link.url'),
            ];
    }

    public function withTokens(): array
    {
        return
            [
                'recipient' => $this->recipient->name,
                'recipient_email' => $this->recipient->system_email,
                'url' => $this->url,
            ];
    }


    public static function requiredTokens(): array
    {
        return ['{!! $url !!}'];
    }

    public static function fakeNotification(): static
    {
        $url = "http://fake.login.link";
        return new static(Auth::user(), $url);
    }

    public function actionLink(): string|null
    {
        return $this->url;
    }
}
