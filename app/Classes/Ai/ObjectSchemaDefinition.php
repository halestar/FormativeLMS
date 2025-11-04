<?php

namespace App\Classes\Ai;

abstract class ObjectSchemaDefinition
{
	abstract public function getModelClass(): string;
	abstract public function fillModel($model): void;
}