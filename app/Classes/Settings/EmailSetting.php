<?php

namespace App\Classes\Settings;

use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Mail\ResetPasswordMail;
use App\Models\Utilities\SystemSetting;
use App\Models\Utilities\WorkFile;
use Illuminate\Database\Eloquent\Casts\Attribute;

class EmailSetting extends SystemSetting implements Fileable
{
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
	
	protected static function booted(): void
	{
		//clean up on save.
		static::saved(function(EmailSetting $setting) {
			$setting->cleanup();
		});
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
	
	public function cleanup()
	{
		//cleans up the files in the conmtent. Essentially it pulls in all the files
		//references in the content and syncs them to this email.
		$fileRefs = [];
		//attempting to match src="https://fablms.app/settings/work-files/(file uuid)"
		$pattern = '@src="' . config('app.url') .
			'/settings/work-files/([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})"@';
		if(preg_match($pattern, $this->content, $fileRefs)) {
			array_shift($fileRefs);
			//we need to iterate through each model and delete each one individually, since that's the way to
			//trigger the file delete for each model.
			foreach(WorkFile::invisible()
			                ->whereNotIn('id', $fileRefs)
			                ->get() as $file)
				$file->delete();
		}
		else {
			//there are no file refs in the content, so we delete all hidden files
			foreach(WorkFile::invisible()
			                ->get() as $file)
				$file->delete();
		}
	}
	
	public function shouldBePublic(): bool
	{
		return true;
	}
	
	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::EmailWork;
	}
}