<?php

namespace App\Classes;

use App\Classes\Settings\SchoolSettings;

class Days
{
	public const MONDAY = 1;
	public const TUESDAY = 2;
	public const WEDNESDAY = 3;
	public const THURSDAY = 4;
	public const FRIDAY = 5;
	public const SATURDAY = 6;
	public const SUNDAY = 7;
	public const ALL = 0;
	
	public static function dayAbbr(int $day): string
	{
		switch($day)
		{
			case self::MONDAY:
				return __('common.days.monday.abbr');
			case self::TUESDAY:
				return __('common.days.tuesday.abbr');
			case self::WEDNESDAY:
				return __('common.days.wednesday.abbr');
			case self::THURSDAY:
				return __('common.days.thursday.abbr');
			case self::FRIDAY:
				return __('common.days.friday.abbr');
			case self::SATURDAY:
				return __('common.days.saturday.abbr');
			case self::SUNDAY:
				return __('common.days.sunday.abbr');
			default:
				return '';
		}
	}
	
	public static function weekdaysOptions(): array
	{
		return array_combine(self::getWeekdays(), self::weekdayNames());
	}
	
	public static function getWeekdays(): array
	{
		return [
			self::MONDAY,
			self::TUESDAY,
			self::WEDNESDAY,
			self::THURSDAY,
			self::FRIDAY,
		];
	}
	
	public static function weekdayNames(): array
	{
		return array_map(fn($day) => self::day($day), self::getWeekdays());
	}
	
	public static function day(int $day): string
	{
		switch($day)
		{
			case self::MONDAY:
				return __('common.days.monday');
			case self::TUESDAY:
				return __('common.days.tuesday');
			case self::WEDNESDAY:
				return __('common.days.wednesday');
			case self::THURSDAY:
				return __('common.days.thursday');
			case self::FRIDAY:
				return __('common.days.friday');
			case self::SATURDAY:
				return __('common.days.saturday');
			case self::SUNDAY:
				return __('common.days.sunday');
			default:
				return '';
		}
	}
	
	public static function weekendsOptions(): array
	{
		return array_combine(self::getWeekends(), self::weekendNames());
	}
	
	public static function getWeekends(): array
	{
		return [
			self::SATURDAY,
			self::SUNDAY,
		];
	}
	
	public static function weekendNames(): array
	{
		return array_map(fn($day) => self::day($day), self::getWeekends());
	}
	
	public static function allOptions(): array
	{
		return array_combine(self::getDays(), self::dayNames());
	}
	
	public static function getDays(): array
	{
		return [
			self::MONDAY,
			self::TUESDAY,
			self::WEDNESDAY,
			self::THURSDAY,
			self::FRIDAY,
			self::SATURDAY,
			self::SUNDAY,
		];
	}
	
	public static function dayNames(): array
	{
		return array_map(fn($day) => self::day($day), self::getDays());
	}
	
	public static function schoolDaysOptions(): array
	{
		return app(SchoolSettings::class)->days;
	}
}
