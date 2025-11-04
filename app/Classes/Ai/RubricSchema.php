<?php

namespace App\Classes\Ai;

use App\Casts\Learning\Rubric;
use NeuronAI\StructuredOutput\SchemaProperty;

class RubricSchema extends ObjectSchemaDefinition
{
	#[SchemaProperty(description: 'The list of criteria to be be evaluated.', required: true)]
	public array $criteria;
	#[SchemaProperty(description: 'The points value assigned to this rubric. Each point value is a single integer and always begin at 0', required: true)]
	public array $points;
	#[SchemaProperty(description: 'The description of how to evaluate the criteria based on the point value. This is an array or arrays with the first dimension belonging to the criteria and the second belonging the number of points for that criteria.', required: true)]
	public array $descriptions;
	public function getModelClass(): string
	{
		return Rubric::class;
	}
	
	public function fillModel($model): void
	{
		$model->criteria = $this->criteria;
		$model->points = $this->points;
		$model->descriptions = $this->descriptions;
	}
}