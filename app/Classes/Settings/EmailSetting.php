<?php

namespace App\Classes\Settings;

use App\Interfaces\Fileable;
use App\Mail\ResetPasswordMail;
use App\Models\Utilities\SystemSetting;
use App\Traits\HasWorkFiles;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmailSetting extends SystemSetting implements Fileable
{
	use HasWorkFiles;
	protected static string $settingKey = "emails.";

	protected static function defaultValue(): array
	{
		return
			[
				"subject" => '',
				"content" => '',
				"setting_name" => '',
				"setting_description" => '',
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
	public function settingName(): Attribute
	{
		return $this->basicProperty('setting_name');
	}
	public function settingDescription(): Attribute
	{
		return $this->basicProperty('setting_description');
	}

	public static function loadEmail(string $key, array $defaults): ?EmailSetting
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