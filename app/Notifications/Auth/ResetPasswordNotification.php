<?php

namespace App\Notifications\Auth;

use App\Models\People\Person;
use App\Models\Utilities\SchoolMessage;
use App\Notifications\LmsNotification;
use Auth;

class ResetPasswordNotification extends LmsNotification
{

    protected Person $recipient;
    protected string $token;
    /**
     * Create a new notification instance.
     */
    public function __construct(Person $recipient, string $token)
    {
        parent::__construct();
        $this->recipient = $recipient;
        $this->token = $token;
    }

    public static function availableTokens(): array
    {
        return
            [
                '{!! $recipient !!}' => __('emails.password.reset.recipient'),
                '{!! $recipient_email !!}' => __('emails.password.reset.recipient_email'),
                '{!! $token !!}' => __('emails.password.reset.token'),
            ];
    }

    public function withTokens(): array
    {
        return
            [
                'recipient' => $this->recipient->name,
                'recipient_email' => $this->recipient->system_email,
                'token' => $this->token,
            ];
    }


    public static function requiredTokens(): array
    {
        return ['{!! $token !!}'];
    }

    public static function fakeNotification(): static
    {
        $token = '123456';
        return new ResetPasswordNotification(Auth::user(), $token);
    }

    public function actionLink(): string|null
    {
        return null;
    }
}
