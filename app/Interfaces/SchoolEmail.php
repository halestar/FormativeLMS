<?php

namespace App\Interfaces;

use App\Classes\Settings\EmailSetting;
use App\Models\Utilities\SystemSetting;

interface SchoolEmail
{
	public static function defaults(): array;
	public function withTokens(): array;
	public static function availableTokens(): array;
	public static function requiredTokens(): array;
	public static function createSettings(): void;
	public static function getSetting(): EmailSetting;
}
