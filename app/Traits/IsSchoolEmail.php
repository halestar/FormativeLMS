<?php

namespace App\Traits;

use App\Classes\Settings\EmailSetting;
use App\Mail\ResetPasswordMail;
use App\Models\Utilities\SystemSetting;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Blade;

trait IsSchoolEmail
{
	public SystemSetting $emailSetting;
	private function loadSettings()
	{
		$this->emailSetting = EmailSetting::loadEmail(static::$key, static::defaults());
		if(!$this->emailSetting->subject || !$this->emailSetting->content)
		{
			if(!$this->emailSetting->subject)
				$this->emailSetting->subject = static::defaults()['subject'];
			if(!$this->emailSetting->content)
				$this->emailSetting->content = static::defaults()['subject'];
			$this->emailSetting->save();
		}
	}
	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(
			subject: $this->emailSetting->subject,
		);
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(
			htmlString: Blade::render($this->emailSetting->content, $this->withTokens()),
		);
	}

	/**
	 * Get the attachments for the message.
	 *
	 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
	 */
	public function attachments(): array
	{
		return [];
	}
}
