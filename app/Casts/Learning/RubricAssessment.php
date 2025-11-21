<?php

namespace App\Casts\Learning;

use App\Casts\Learning\Rubric;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;

class RubricAssessment implements CastsAttributes, Arrayable, JsonSerializable
{
	public array $criteria, $points, $descriptions, $scores;

	public function __construct(Rubric $rubric = null)
	{
		$this->criteria = $rubric?->criteria ?? [];
		$this->points = $rubric?->points ?? [];
		$this->descriptions = $rubric?->descriptions ?? [];
		$this->scores = array_fill(0, count($this->points), null);
	}

	/**
	 * METHOD IMPLEMENTATIONS
	 */

	/**
	 * Cast the given value.
	 *
	 * @param array<string, mixed> $attributes
	 */
	public function get(Model $model, string $key, mixed $value, array $attributes): mixed
	{
		$data = json_decode($value, true);
		if($data && isset($data['criteria']))
			return RubricAssessment::hydrate($data);
		return null;
	}

	public static function hydrate(array $data): RubricAssessment
	{
		$rubric = new RubricAssessment();
		$rubric->criteria = $data['criteria'];
		$rubric->points = $data['points'];
		$rubric->descriptions = $data['descriptions'];
		$rubric->scores = $data['scores'] ?? array_fill(0, count($data['points']), null);
		return $rubric;
	}

	/**
	 * Prepare the given value for storage.
	 *
	 * @param array<string, mixed> $attributes
	 */
	public function set(Model $model, string $key, mixed $value, array $attributes): mixed
	{
		return json_encode($value->toArray());
	}

	public function toArray()
	{
		return
			[
				'criteria' => $this->criteria,
				'points' => $this->points,
				'descriptions' => $this->descriptions,
				'scores' => $this->scores,
			];
	}

	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
}