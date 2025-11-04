<?php

namespace App\Classes\Attributes;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class SelectableSource
{
	public function __construct(public string $className){}
}