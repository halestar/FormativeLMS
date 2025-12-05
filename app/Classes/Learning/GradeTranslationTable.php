<?php

namespace App\Classes\Learning;

use App\Interfaces\Synthesizable;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class GradeTranslationTable implements Synthesizable
{
	public Collection $rows;
	public int $filter = 0;
	
	public function __construct($rows = [])
	{
		$this->rows = new Collection();
		foreach($rows as $row)
			$this->rows->push(new GradeTranslationRow($row));
	}
	
	public function toArray(): array
	{
		$r = [];
		foreach($this->rows as $row)
			$r[] = $row->toArray();
		return $r;
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public function get(Model $model, string $key, mixed $value, array $attributes)
	{
		$json = json_decode($value, true);
		return new GradeTranslationTable($json);
	}
	
	public function set(Model $model, string $key, mixed $value, array $attributes)
	{
		return json_encode($value->jsonSerialize());
	}
	
	public function opportunities(): self
	{
		$this->filter = GradeTranslationRow::APPLIES_OPPORTUNITY;
		return $this;
	}
	public function criteria(): self
	{
		$this->filter = GradeTranslationRow::APPLIES_CRITERIA;
		return $this;
	}
	public function overall(): self
	{
		$this->filter = GradeTranslationRow::APPLIES_OVERALL;
		return $this;
	}
	public function reports(): self
	{
		$this->filter = GradeTranslationRow::APPLIES_REPORTS;
		return $this;
	}
	public function transcripts(): self
	{
		$this->filter = GradeTranslationRow::APPLIES_TRANSCRIPTS;
		return $this;
	}
	
	public function displayGrade(float $grade): string
	{
		$filter = $this->filter;
		if($filter == 0)
			return $this->rows->first(fn(GradeTranslationRow $row) => $row->applies($grade))?->grade ??  __('learning.grades.none');
		return $this->rows->filter(fn(GradeTranslationRow $row) => $row->appliesTo($filter))
			->first(fn(GradeTranslationRow $row) => $row->applies($grade))?->grade ?? __('learning.grades.none');
	}
	
	public function addRow(): void
	{
		$this->rows->push(new GradeTranslationRow([]));
	}
	
	public function removeRow(int $index): void
	{
		$this->rows->splice($index, 1);
	}
	
	public function updateRow(int $index, GradeTranslationRow $row): void
	{
		$this->rows[$index] = $row;
	}

    public static function hydrate(array $data): static
    {
        return new static($data);
    }
}