<?php

namespace App\Classes\People;

use App\Interfaces\Synthesizable;

class NameToken implements Synthesizable
{
	
	public const TYPE_BASIC_FIELD = 1;
	public const TYPE_ROLE_FIELD = 2;
	public const TYPE_TEXT = 3;
	public int $type;
	public ?string $textContent = null;
	public ?string $basicFieldName = null;
	public ?RoleField $roleField = null;
	public ?int $roleId = null;
	public bool $spaceAfter = true;
	
	public function __construct(int $type, array $attributes = null)
	{
		$this->type = $type;
		if($attributes)
		{
			$this->type = $attributes['type'] ?? self::TYPE_TEXT;
			$this->textContent = $attributes['textContent'] ?? null;
			$this->basicFieldName = $attributes['basicFieldName'] ?? null;
			$this->roleField = $attributes['roleField'] ?? null;
			$this->spaceAfter = $attributes['spaceAfter'] ?? true;
			$this->roleId = $attributes['roleId'] ?? null;
		}
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	/**
	 * @inheritDoc
	 */
	public function toArray(): array
	{
		return
			[
                'type' => $this->type,
                'textContent' => $this->textContent,
                'basicFieldName' => $this->basicFieldName,
                'roleField' => $this->roleField ? $this->roleField->toArray() : null,
                'spaceAfter' => $this->spaceAfter,
                'roleId' => $this->roleId,
			];
	}
	
	public function __toString()
	{
		$def = "";
		if($this->type == NameToken::TYPE_BASIC_FIELD)
			$def = "[" . $this->basicFields()[$this->basicFieldName] . "]";
		elseif($this->type == NameToken::TYPE_ROLE_FIELD)
			$def = "[" . $this->roleField->fieldName . "]";
		else
			$def = $this->textContent;
		if($this->spaceAfter)
			$def .= " ";
		return $def;
	}
	
	public static function basicFields(): array
	{
		return
			[
				'first' => __('people.profile.fields.first'),
				'middle' => __('people.profile.fields.middle'),
				'last' => __('people.profile.fields.last'),
				'nick' => __('people.profile.fields.nick'),
				'email' => __('people.profile.fields.email'),
				'preferred_first' => __('people.profile.fields.preferred_first'),
			];
	}

    public static function hydrate(array $data): static
    {
        $instance = new NameToken($data['type']);

        $instance->textContent = $data['textContent'];
        $instance->basicFieldName = $data['basicFieldName'];
        $instance->roleField = $data['roleField'] ? new RoleField($data['roleField']) : null;
        $instance->spaceAfter = $data['spaceAfter'];
        $instance->roleId = $data['roleId'];
        return $instance;
    }
}
