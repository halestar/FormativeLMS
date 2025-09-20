<?php

namespace App\Interfaces;

use App\Classes\Settings\EmailSetting;
use App\Models\People\Person;
use App\Traits\IsSchoolEmail;
use Illuminate\Mail\Mailable;

interface SchoolEmail
{
	/**
	 * @return array The default values for this email.
	 */
	public static function defaults(): array;
	
	/**
	 * @return array<string, string> The tokens that are available to be used in the email.
	 */
	public static function availableTokens(): array;
	
	/**
	 * @return array<string> The tokens that are required to be used in the email.
	 */
	public static function requiredTokens(): array;
	
	/**
	 * Creates the settings for this email. Only Used when seeding and defined in the
	 * IsSchoolEmail trait.
	 * @see IsSchoolEmail
	 */
	public static function createSettings(): void;
	
	/**
	 * @return EmailSetting The persistable settings for this email. Defined in the IsSchoolEmail trait.
	 * @see IsSchoolEmail
	 */
	public static function getSetting(): EmailSetting;
	
	/**
	 * @return Mailable returns a fake test email that can be sent to the attached person.
	 */
	public static function testEmail(Person $person): Mailable;
	
	/**
	 * @return array<string, mixed> The tokens passed to the blade view for this email.
	 */
	public function withTokens(): array;
}
