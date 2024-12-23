<?php

namespace App\Classes;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class RoleField implements Arrayable, JsonSerializable
{
    public const TEXT = "TEXT";
    public const EMAIL = "EMAIL";
    public const DATE = "DATE";
    public const DATETIME = "DATETIME";
    public const URL = "URL";
    public const SELECT = "SELECT";
    public const CHECKBOX = "CHECKBOX";
    public const RADIO = "RADIO";
    public const TEXTAREA = "TEXTAREA";

    public const FIELDS =
        [
            RoleField::TEXT => "people.fields.types.text",
            RoleField::EMAIL => "people.fields.types.email",
            RoleField::DATE => "people.fields.types.date",
            RoleField::DATETIME => "people.fields.types.datetime",
            RoleField::URL => "people.fields.types.url",
            RoleField::SELECT => "people.fields.types.select",
            RoleField::CHECKBOX => "people.fields.types.checkbox",
            RoleField::RADIO => "people.fields.types.radio",
            RoleField::TEXTAREA => "people.fields.types.textarea",
        ];

    public string $fieldId;
    public string $fieldName = "";
    public string $fieldType;
    public string $fieldHelp;
    public string|array $fieldPlaceholder;
    public array $fieldOptions = [];
    public null|string|array $fieldValue = null;
    public ?int $roleId;

    private function generateId(): string
    {
        return strtolower(preg_replace('/\s+/', '_', $this->fieldName));
    }
    public function __construct($attributes = null)
    {
        if($attributes)
        {
            $this->fieldName = $attributes['fieldName']?? "";
            $this->fieldType = $attributes['fieldType']?? RoleField::FIELDS[RoleField::TEXT];
            $this->fieldHelp = $attributes['fieldHelp']?? "";
            if($this->fieldType == RoleField::CHECKBOX)
                $this->fieldPlaceholder = $attributes['fieldPlaceholder']?? [];
            else
                $this->fieldPlaceholder = $attributes['fieldPlaceholder']?? "";
            $this->fieldOptions = $attributes['fieldOptions']?? [];
            $this->fieldValue = $attributes['fieldValue']?? null;
            $this->roleId = $attributes['roleId']?? null;
        }
        $this->fieldId = $this->generateId();
    }


    public function toArray()
    {
        $this->fieldId = $this->generateId();
        return
            [
                'fieldId' => $this->fieldId,
                'fieldName' => $this->fieldName,
                'fieldType' => $this->fieldType,
                'fieldHelp' => $this->fieldHelp,
                'fieldPlaceholder' => $this->fieldPlaceholder,
                'fieldOptions' => $this->fieldOptions,
                'fieldValue' => $this->fieldValue,
            ];
    }

    public function jsonSerialize(): mixed
    {
        if($this->fieldId)
            return [$this->fieldId => $this->toArray()];
        return $this->toArray();
    }

    public function getHtml(): string
    {
        if($this->fieldType == RoleField::SELECT)
        {
            $input = '<div class="mb-3"><label for="' . $this->fieldId . '" class="form-label">' .
                $this->fieldName . '</label><select name="'.$this->fieldId.'" class="form-select" ' .
                ' aria-describedby="' . $this->fieldId . '_help" id="' . $this->fieldId . '">';
            foreach($this->fieldOptions as $option)
            {
                $input .= '<option value="' . $option . '" ';
                if($this->fieldValue !== null)
                {
                    if($option == $this->fieldValue)
                        $input .= 'selected';
                }
                elseif($option == $this->fieldPlaceholder)
                    $input .= 'selected';
                $input .= '>' . $option . '</option>';
            }
            $input .= '</select>' .
                '<div class="form-text" id="' . $this->fieldId . '_help">' . $this->fieldHelp . '</div></div>';
            return $input;
        }
        elseif($this->fieldType == RoleField::CHECKBOX)
        {
            $input = '<div class="mb-3"><label class="form-label" aria-describedby="' .
                $this->fieldId . '_help">' . $this->fieldName . '</label><br />';
            $idx = 0;
            foreach($this->fieldOptions as $option)
            {
                $input .= '<div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" value="' .
                    $option . '" id="' . $this->fieldId . $idx . '" name="' . $this->fieldId .'[]" ';
                if($this->fieldValue !== null)
                {
                    if(in_array($option, $this->fieldValue))
                        $input .= 'checked';
                }
                elseif(in_array($option, $this->fieldPlaceholder))
                    $input .= 'checked';
                $input .= ' /><label class="form-check-label" for="' . $this->fieldId . $idx . '">' . $option . '</label></div>';
                $idx++;
            }
            $input .= '<div class="form-text" id="' . $this->fieldId . '_help">' . $this->fieldHelp . '</div></div>';
            return $input;
        }
        elseif($this->fieldType == RoleField::RADIO)
        {
            $input = '<div class="mb-3"><label class="form-label aria-describedby="' .
                $this->fieldId . '_help">' . $this->fieldName . '</label><br />';
            $idx = 0;
            foreach($this->fieldOptions as $option)
            {
                $input .= '<div class="form-check form-check-inline"><input class="form-check-input" type="radio" value="' .
                    $option . '" id="' . $this->fieldId . $idx . '" name="' . $this->fieldId .'" ';
                if($this->fieldValue !== null)
                {
                    if($option == $this->fieldValue)
                        $input .= 'checked';
                }
                elseif($option == $this->fieldPlaceholder)
                    $input .= 'checked';
                $input .= ' /> <label class="form-check-label" for="' . $this->fieldId . $idx . '">' . $option . '</label></div>';
                $idx++;
            }
            $input .= '<div class="form-text" id="' . $this->fieldId . '_help">' . $this->fieldHelp . '</div></div>';
            return $input;
        }
        elseif($this->fieldType == RoleField::TEXTAREA)
        {
            $input = '<div class="mb-3"><label for="' . $this->fieldId . '" class="form-label">' .
                $this->fieldName . '</label><textarea name="'.$this->fieldId.'" class="form-control" placeholder="' .
                $this->fieldPlaceholder . '"' .
                ' aria-describedby="' . $this->fieldId . '_help">' . $this->fieldValue . '</textarea>' .
                '<div class="form-text" id="' . $this->fieldId . '_help">' . $this->fieldHelp . '</div></div>';
            return $input;
        }
        //in this case it's a type of text input, which we handle the same
        $input = '<div class="mb-3"><label for="' . $this->fieldId . '" class="form-label">' .
            $this->fieldName . '</label><input type="';
        switch($this->fieldType)
        {
            case RoleField::TEXT: $input .= 'text"'; break;
            case RoleField::EMAIL: $input .= 'email"'; break;
            case RoleField::DATE: $input .= 'date"'; break;
            case RoleField::DATETIME: $input .= 'datetime-local"'; break;
            case RoleField::URL: $input .= 'url"'; break;
        }
        $input .= ' name="'.$this->fieldId.'" class="form-control" placeholder="' . $this->fieldPlaceholder . '"' .
            ' aria-describedby="' . $this->fieldId . '_help" value="' . $this->fieldValue . '"/>' .
            '<div class="form-text" id="' . $this->fieldId . '_help">' . $this->fieldHelp . '</div></div>';
        return $input;
    }


}
