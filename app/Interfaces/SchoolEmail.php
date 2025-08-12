<?php

namespace App\Interfaces;

interface SchoolEmail
{
	public static function defaults(): array;
	public function withTokens(): array;
	public static function availableTokens(): array;
	public static function requiredTokens(): array;
	public static function emailName(): string;
	public static function emailDescription(): string;
}
