<?php

namespace App\Classes\People;

use App\Interfaces\Synthesizable;
use App\Models\People\Person;

class NameConstructor implements Synthesizable
{
	
	public function __construct(public array $tokens) {}
	
	public function applyName(Person $person): string
	{
		$name = "";
		foreach($this->tokens as $token)
		{
			if($token->type == NameToken::TYPE_BASIC_FIELD)
			{
				$basicFieldName = $token->basicFieldName;
				$fieldValue = $person->$basicFieldName;
				if($fieldValue && $fieldValue != "")
					$name .= $fieldValue . ($token->spaceAfter ? " " : "");
			}
			elseif($token->type == NameToken::TYPE_ROLE_FIELD)
			{
				$role = $person->schoolRoles()
				               ->where('roles.id', $token->roleId)
				               ->first();
				$fieldValue = $role->fields[$token->roleField->fieldId]->fieldValue;
				if($fieldValue && $fieldValue != "")
					$name .= $fieldValue . ($token->spaceAfter ? " " : "");
			}
			else
			{
				$fieldValue = $token->textContent;
				if($fieldValue && $fieldValue != "")
					$name .= $fieldValue . ($token->spaceAfter ? " " : "");
			}
		}
		return $name;
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public function toArray(): array
	{
		return
			[
				'tokens' => $this->tokens,
			];
	}
	
	public function __toString()
	{
		$def = "";
		foreach($this->tokens as $token)
			$def .= $token->__toString();
		return $def;
	}

    public static function hydrate(array $data): static
    {
        return new NameConstructor($data['tokens']);
    }
}
