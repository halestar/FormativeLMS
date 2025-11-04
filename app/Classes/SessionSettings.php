<?php

namespace App\Classes;

use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use App\Models\People\Person;
use Illuminate\Support\Facades\Auth;

class SessionSettings
{
	private const SETTING_KEY = 'session_settings_';
	public static SessionSettings $instance;
	private Person $person;
	private string $sessionKey;
	private array $settings = [];
	
	private function __construct(Person $person)
	{
		$this->person = $person;
		$this->sessionKey = self::SETTING_KEY . $person->id;
		$this->settings = session($this->sessionKey, []);
		//there are some variables we can preset
		if(!isset($this->settings['working_campus_id']))
		{
			//determine the working campus. For emplyees, it's the first campus we get back
			if($person->isEmployee())
				$this->settings['working_campus_id'] = $person->employeeCampuses()
				                                              ->first()->id;
			//for students there's only one campus
			elseif($person->isStudent())
				$this->settings['working_campus_id'] = $person->student()->campus->id;
			//for parents, its the first one in the parental relationships
			elseif($person->isParent())
				$this->settings['working_campus_id'] = $person->parentCampuses()
				                                              ->first()->id;
		}
		//year is easy, it's always the current one
		if(!isset($this->settings['working_year_id']))
			$this->settings['working_year_id'] = Year::currentYear()->id;
		//terms is the same as years.
		if(!isset($this->settings['working_term_id']) && isset($this->settings['working_campus_id']))
			$this->settings['working_term_id'] = Term::currentTerm(Campus::find($this->settings['working_campus_id']))->id;
		self::$instance = $this;
	}
	
	public static function workingCampus(null|int|Campus $campus = null): ?Campus
	{
		if(!$campus)
			$campus = self::get('working_campus_id');
		elseif(is_int($campus))
			self::set('working_campus_id', $campus);
		else
		{
			self::set('working_campus_id', $campus->id);
			return $campus;
		}
		return Campus::find($campus);
	}
	
	public static function get(string $key, $default = null)
	{
		$instance = self::instance();
		if(isset($instance->settings[$key]))
			return $instance->settings[$key];
		$instance::set($key, $default);
		return $default;
	}
	
	public static function instance(): SessionSettings
	{
		if(isset(self::$instance))
			return self::$instance;
		return new SessionSettings(Auth::user());
	}
	
	public static function set(string $key, $value)
	{
		$instance = self::instance();
		$instance->settings[$key] = $value;
		session([$instance->sessionKey => $instance->settings]);
	}
	
	
	public static function workingYear(null|int|Year $year = null): ?Year
	{
		if(!$year)
			$year = self::get('working_year_id');
		elseif(is_int($year))
			self::set('working_year_id', $year);
		else
		{
			self::set('working_year_id', $year->id);
			return $year;
		}
		return Year::find($year);
	}
	
	public static function workingTerm(null|int|Term $term = null): ?Term
	{
		if(!$term)
			$term = self::get('working_term_id');
		elseif(is_int($term))
			self::set('working_term_id', $term);
		else
		{
			self::set('working_term_id', $term->id);
			return $term;
		}
		return Term::find($term);
	}
	
	public static function has(string $key): bool
	{
		$instance = self::instance();
		return isset($instance->settings[$key]);
	}
	
	public function getPageSetting(string $page, string $setting, mixed $default = null): mixed
	{
		$setting = $this->get($page, []);
		return $setting[$setting] ?? $default;
	}
	
	public function setPageSetting(string $page, string $setting, mixed $value): void
	{
		$setting = $this->get($page, []);
		$setting[$setting] = $value;
		$this->set($page, $setting);
	}
}
