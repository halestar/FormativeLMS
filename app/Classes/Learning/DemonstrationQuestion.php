<?php

namespace App\Classes\Learning;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class DemonstrationQuestion implements Arrayable, JsonSerializable
{
	public string $question = "";
	public int $type = self::TYPE_SHORT;
	public array $options = [];
	public const TYPE_SHORT = 1;
	public const TYPE_LONG = 2;
	public const TYPE_MULTIPLE = 3;
	public const TYPE_TRUE_FALSE = 4;
	public const TYPE_CHOICE = 5;
	
	public static function typeOptions(): array
	{
		return [
			self::TYPE_SHORT => __('learning.demonstrations.questions.type.short'),
			self::TYPE_LONG => __('learning.demonstrations.questions.type.long'),
			self::TYPE_MULTIPLE => __('learning.demonstrations.questions.type.multiple'),
			self::TYPE_CHOICE => __('learning.demonstrations.questions.type.choice'),
			self::TYPE_TRUE_FALSE => __('learning.demonstrations.questions.type.tf'),
		];
	}
    
    public function __construct(int $type = self::TYPE_SHORT)
    {
        $this->question = '';
		$this->type = $type;
		$this->options = [];
    }
	
	public function toArray()
	{
		return ['question' => $this->question, 'type' => $this->type, 'options' => $this->options];
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public static function hydrate(array $data): self
	{
		$question = new self($data['type']);
		$question->question = $data['question'];
		$question->options = $data['options'] ?? [];
		return $question;
	}

	public function hasOptions(): bool
	{
		return count($this->options) > 0;
	}
}
