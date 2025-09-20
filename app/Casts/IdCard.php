<?php

namespace App\Casts;

use App\Classes\IdCard\IdCardElement;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use JsonSerializable;

class IdCard implements CastsAttributes, Arrayable, JSONSerializable
{
	public const CONTENT_EMPTY = 1;
	public const CONTENT_SPAN = 2;
	public static array $sizes = [1.0, 1.59, 1.4, 1.48, 0.63, 0.71, 0.68];
	public float $aspectRatio;
	public ?string $backgroundImage = null;
	public string $backgroundColor = "#ffffff";
	public float $backgroundImageOpacity = 1.0;
	public string $backgroundBlendMode = "normal";
	public int $rows = 4;
	public int $columns = 4;
	public int $contentPadding = 0;
	public string $preview = "";
	private array $content;
	
	public function __construct()
	{
		$this->aspectRatio = 1.0;
		$this->content = [];
		for($i = 0; $i < $this->rows; $i++)
		{
			$row = [];
			for($j = 0; $j < $this->columns; $j++)
				$row[] = self::CONTENT_EMPTY;
			$this->content[] = $row;
		}
	}
	
	public function getContent($row, $column)
	{
		return $this->content[$row][$column];
	}
	
	public function updateRowCount(int $rowCount)
	{
		//more or less?
		if($rowCount > $this->rows)
		{
			//more is easier, just add the extra row
			$newCol = [];
			for($i = 0; $i < $this->columns; $i++)
				$newCol[] = self::CONTENT_EMPTY;
			for($i = 0; $i < $rowCount - $this->rows; $i++)
				$this->content[] = $newCol;
		}
		else
		{
			//in this case, we slice the array, dropping the bottom
			$this->content = array_slice($this->content, 0, $rowCount);
		}
		$this->rows = $rowCount;
	}
	
	public function updateColumnCount(int $columnCount)
	{
		//more or less?
		if($columnCount > $this->columns)
		{
			//more is harder, we need to add the extra columns
			for($i = 0; $i < $this->rows; $i++)
			{
				for($j = 0; $j < $columnCount - $this->columns; $j++)
					$this->content[$i][] = self::CONTENT_EMPTY;
			}
		}
		else
		{
			//in this case, we slice the end of the columns for each row
			for($i = 0; $i < $this->rows; $i++)
			{
				$this->content[$i] = array_slice($this->content[$i], 0, $columnCount);
			}
		}
		$this->columns = $columnCount;
	}
	
	public function moveElement(int $fromRow, int $fromCol, int $toRow, int $toCol): void
	{
		//save the element.
		$element = $this->content[$fromRow][$fromCol];
		$this->removeElement($fromRow, $fromCol);
		$this->addElement($element, $toRow, $toCol);
	}
	
	public function removeElement(int $row, int $column): void
	{
		$colSpan = $this->content[$row][$column]->colSpan;
		$rowSpan = $this->content[$row][$column]->rowSpan;
		for($i = 0; $i < $rowSpan && ($i + $row) < $this->rows; $i++)
		{
			for($j = 0; $j < $colSpan && ($j + $column) < $this->columns; $j++)
			{
				$this->content[$i + $row][$j + $column] = self::CONTENT_EMPTY;
			}
		}
	}
	
	public function addElement(IdCardElement $element, int $row, int $column): void
	{
		$this->content[$row][$column] = $element;
		$this->updateSpan($row, $column);
	}
	
	public function updateSpan(int $elementRow, int $elementColumn)
	{
		$element = $this->content[$elementRow][$elementColumn];
		if($element->rowSpan > 1 || $element->colSpan > 1)
		{
			for($i = 0; $i < $element->rowSpan && ($i + $elementRow) < $this->rows; $i++)
			{
				for($j = 0; $j < $element->colSpan && ($j + $elementColumn) < $this->columns; $j++)
				{
					if($i == 0 && $j == 0)
						continue;
					$this->content[$i + $elementRow][$j + $elementColumn] = self::CONTENT_SPAN;
				}
			}
		}
	}
	
	public function setElementRowSpan(int $row, int $column, int $rowSpan): void
	{
		$element = $this->content[$row][$column];
		if($rowSpan < 1)
			$rowSpan = 1;
		elseif($rowSpan > ($this->rows - $row))
			$rowSpan = ($this->rows - $row);
		$this->removeElement($row, $column);
		$element->rowSpan = $rowSpan;
		$this->addElement($element, $row, $column);
	}
	
	public function setElementColSpan(int $row, int $column, int $colSpan): void
	{
		$element = $this->content[$row][$column];
		if($colSpan < 1)
			$colSpan = 1;
		elseif($colSpan > ($this->columns - $column))
			$colSpan = ($this->columns - $column);
		$this->removeElement($row, $column);
		$element->colSpan = $colSpan;
		$this->addElement($element, $row, $column);
	}
	
	public function getBackgroundRbga(): string
	{
		list($r, $g, $b) = sscanf($this->backgroundColor, "#%02x%02x%02x");
		return "rgba(" . $r . ", " . $g . ", " . $b . ", " . $this->backgroundImageOpacity . ")";
	}
	
	public function jsonSerialize(): mixed
	{
		return $this->toArray();
	}
	
	public function toArray()
	{
		$content = [];
		foreach($this->content as $row)
		{
			$rowContent = [];
			foreach($row as $element)
			{
				if($element instanceof IdCardElement)
				{
					$c = $element->toArray();
					$c['className'] = $element::class;
					$rowContent[] = $c;
				}
				else
					$rowContent[] = $element;
			}
			$content[] = $rowContent;
		}
		
		return
			[
				'aspectRatio' => $this->aspectRatio,
				'backgroundImage' => $this->backgroundImage,
				'backgroundColor' => $this->backgroundColor,
				'rows' => $this->rows,
				'columns' => $this->columns,
				'content' => $content,
				'contentPadding' => $this->contentPadding,
				'backgroundImageOpacity' => $this->backgroundImageOpacity,
				'backgroundBlendMode' => $this->backgroundBlendMode,
				'preview' => $this->preview,
			];
	}
	
	public function get(Model $model, string $key, mixed $value, array $attributes)
	{
		$data = json_decode($value, true);
		if($data)
			return IdCard::hydrate($data);
		return null;
	}
	
	public static function hydrate(array $data): IdCard
	{
		$idCard = new IdCard();
		$idCard->aspectRatio = $data['aspectRatio'];
		$idCard->backgroundImage = $data['backgroundImage'];
		$idCard->backgroundColor = $data['backgroundColor'];
		$idCard->rows = $data['rows'];
		$idCard->columns = $data['columns'];
		$idCard->contentPadding = $data['contentPadding'];
		$idCard->backgroundImageOpacity = $data['backgroundImageOpacity'];
		$idCard->backgroundBlendMode = $data['backgroundBlendMode'];
		$idCard->preview = $data['preview'] ?? '';
		$idCard->content = [];
		foreach($data['content'] as $row)
		{
			$rowContent = [];
			foreach($row as $element)
			{
				if(isset($element['className']))
				{
					$className = $element['className'];
					$rowContent[] = ($className)::hydrate($element);
				}
				else
					$rowContent[] = $element;
			}
			$idCard->content[] = $rowContent;
		}
		return $idCard;
	}
	
	public function set(Model $model, string $key, mixed $value, array $attributes)
	{
		return json_encode($value->toArray());
	}
	
	public function generatePreview()
	{
		$this->preview = Blade::render('people.ids.dummy', ['idCard' => $this]);
	}
}
