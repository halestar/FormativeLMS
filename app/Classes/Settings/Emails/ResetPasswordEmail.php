<?php

namespace App\Classes\Settings\Emails;

use App\Classes\Settings\EmailSetting;
use Illuminate\Support\Facades\Blade;

class ResetPasswordEmail extends EmailSetting
{

	public static function emailSettingName(): string
	{
		return __('emails.password.reset');
	}

	public static function emailSettingDescription(): string
	{
		return __('emails.password.reset.description');
	}

	public function render(): string
	{
		return Blade::render($this->content);
	}
}