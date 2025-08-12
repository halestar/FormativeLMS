<?php

namespace App\Classes\Settings;

use App\Classes\Auth\AuthenticationDesignation;
use App\Mail\ResetPasswordMail;
use App\Models\Utilities\SystemSetting;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmailSetting extends SystemSetting
{
	protected static string $settingKey = "emails.";

	protected static function defaultValue(): array
	{
		return
			[
				"subject" => '',
				"content" => '',
			];
	}
	public function content(): Attribute
	{
		return $this->basicProperty('content');
	}
	public function subject(): Attribute
	{
		return $this->basicProperty('subject');
	}

	public static function loadEmail(string $key, array $defaults): ?SystemSetting
	{
		$template = EmailSetting::where('name', EmailSetting::$settingKey . $key)->first();
		if(!$template)
		{
			$template = new EmailSetting();
			$template->name = EmailSetting::$settingKey . $key;
			$template->value = $defaults;
			$template->save();
		}
		return $template;
	}

	public static function allEmails(): array
	{
		return
		[
			ResetPasswordMail::class,
		];
	}
}