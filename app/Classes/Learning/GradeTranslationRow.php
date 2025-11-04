<?php

namespace App\Classes\Learning;

use Illuminate\Contracts\Support\Arrayable;

class GradeTranslationRow implements Arrayable, \JsonSerializable
{
	public const APPLIES_OPPORTUNITY = 1;
	public const APPLIES_CRITERIA = 2;
	public const APPLIES_OVERALL = 3;
	public const APPLIES_REPORTS = 4;
	public const APPLIES_TRANSCRIPTS = 5;
	public float $min;
	public float $max;
	public string $grade;
	public bool $appliesToOpportunities, $appliesToCriteria, $appliesToOverall, $appliesToReports, $appliesToTranscripts;
	public function __construct($row)
	{
		$this->min = $row['min']?? 0;
		$this->max = $row['max']?? 0;
		$this->grade = $row['grade']?? __('learning.grades.new');
		$this->appliesToOpportunities = $row['appliesToOpportunities']?? true;
		$this->appliesToCriteria = $row['appliesToCriteria']?? true;
		$this->appliesToOverall = $row['appliesToOverall']?? true;
		$this->appliesToReports = $row['appliesToReports']?? true;
		$this->appliesToTranscripts = $row['appliesToTranscripts']?? true;
	}
	
	public function appliesTo(int $type): bool
	{
		switch($type)
		{
			case self::APPLIES_OPPORTUNITY:
				return $this->appliesToOpportunities;
			case self::APPLIES_CRITERIA:
				return $this->appliesToCriteria;
			case self::APPLIES_OVERALL:
				return $this->appliesToOverall;
			case self::APPLIES_REPORTS:
				return $this->appliesToReports;
			case self::APPLIES_TRANSCRIPTS:
				return $this->appliesToTranscripts;
		}
		return false;
	}
	
	public function setAppliesTo(int $type, bool $value = true): void
	{
		switch($type)
		{
			case self::APPLIES_OPPORTUNITY:
				$this->appliesToOpportunities = $value;
				break;
			case self::APPLIES_CRITERIA:
				$this->appliesToCriteria = $value;
				break;
			case self::APPLIES_OVERALL:
				$this->appliesToOverall = $value;
				break;
			case self::APPLIES_REPORTS:
				$this->appliesToReports = $value;
				break;
			case self::APPLIES_TRANSCRIPTS:
				$this->appliesToTranscripts = $value;
				break;
		}
	}
	
	public function toArray()
	{
		return
			[
				'min' => $this->min,
				'max' => $this->max,
				'grade' => $this->grade,
				'appliesToOpportunities' => $this->appliesToOpportunities,
				'appliesToCriteria' => $this->appliesToCriteria,
				'appliesToOverall' => $this->appliesToOverall,
				'appliesToReports' => $this->appliesToReports,
				'appliesToTranscripts' => $this->appliesToTranscripts,
			];
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public function applies(float $value): bool
	{
		return ($value >= $this->min && $value <= $this->max);
	}
}