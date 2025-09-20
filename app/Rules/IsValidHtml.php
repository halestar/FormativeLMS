<?php

namespace App\Rules;

use Closure;
use DOMDocument;
use Illuminate\Contracts\Validation\ValidationRule;

class IsValidHtml implements ValidationRule
{
	/**
	 * Run the validation rule.
	 *
	 * @param \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString $fail
	 */
	public function validate(string $attribute, mixed $value, Closure $fail): void
	{
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($value);
		libxml_use_internal_errors(false);
		$errors = libxml_get_errors();
		libxml_clear_errors();
		if(count($errors) > 0)
			$fail('errors.attribute.html')->translate();
	}
}
