<?php

namespace App\Mail;

use App\Classes\Settings\EmailSetting;
use App\Interfaces\SchoolEmail;
use App\Models\People\Person;
use App\Models\Utilities\SystemSetting;
use App\Traits\IsSchoolEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class ResetPasswordMail extends Mailable implements SchoolEmail, ShouldQueue
{
    use Queueable, SerializesModels, IsSchoolEmail;
	public Person $recipient;
	public string $token;
	private static string $key = 'passwords.reset';
	
	/**
	 * Create a new message instance.
	 */
	public function __construct(Person $recipient, string $token)
	{
		$this->recipient = $recipient;
		$this->token = $token;
		$this->loadSettings();
	}

	public static function defaults(): array
	{
		return
		[
			'subject' => __('emails.password.reset.subject'),
			'content' => __('emails.password.reset.body'),
			"setting_name" => __('emails.password.reset'),
			"setting_description" => __('emails.password.reset.description'),
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

	public static function availableTokens(): array
	{
		return
		[
			'recipient' => __('emails.password.reset.recipient'),
			'recipient_email' => __('emails.password.reset.recipient_email'),
			'token' => __('emails.password.reset.token'),
		];
	}

	public static function requiredTokens(): array
	{
		return [ 'token' ];
	}
}
