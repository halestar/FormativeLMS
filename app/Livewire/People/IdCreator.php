<?php

namespace App\Livewire\People;

use App\Casts\IdCard;
use App\Classes\IdCard\IdCardElement;
use App\Classes\Settings\IdSettings;
use App\Models\Locations\Campus;
use App\Models\People\Person;
use App\Models\Utilities\SchoolRoles;
use Livewire\Component;

class IdCreator extends Component
{
	public IdCard $idCard;
	public IdCard $originalCard;
	public bool $isSaved;
	public int $selectedRow = -1;
	public int $selectedCol = -1;
	public ?IdCardElement $selectedElement = null;
	public int $idWidth = 600;
	public bool $viewing = false;
	public Person $viewingPerson;
	public string $viewingSize = "md";
	public array $imports;
	public string $importName;
	
	public function mount(IdCard $schoolIdCard, IdSettings $idSettings)
	{
		$this->idCard = $schoolIdCard;
		$this->originalCard = clone $schoolIdCard;
		$this->isSaved = true;
		$this->viewingPerson = auth()->user();
		//create the import array
		$this->imports = [];
		//global
		if($idSettings->getGlobalId()->preview)
			$this->imports[trans_choice('people.id.global', 1)] = $idSettings->getGlobalId();
		//roles
		if($idSettings->getRoleId(SchoolRoles::StudentRole())->preview)
			$this->imports[__('people.id.student')] = $idSettings->getRoleId(SchoolRoles::StudentRole());
		if($idSettings->getRoleId(SchoolRoles::ParentRole())->preview)
			$this->imports[__('people.id.parent')] = $idSettings->getRoleId(SchoolRoles::ParentRole());
		if($idSettings->getRoleId(SchoolRoles::EmployeeRole())->preview)
			$this->imports[__('people.id.employee')] = $idSettings->getRoleId(SchoolRoles::EmployeeRole());
		//campuses and both are next
		$both = [];
		foreach(Campus::all() as $campus)
		{
			if($idSettings->getCampusId($campus)->preview)
				$this->imports[__('people.id.campus', ['campus' => $campus->name])] =
					$idSettings->getCampusId($campus);
			
			if($idSettings->getRoleCampusId(SchoolRoles::StudentRole(), $campus)->preview)
				$both[__('people.id.both.student', ['campus' => $campus->name])] =
					$idSettings->getRoleCampusId(SchoolRoles::StudentRole(), $campus);
			
			if($idSettings->getRoleCampusId(SchoolRoles::ParentRole(), $campus)->preview)
				$both[__('people.id.both.parent', ['campus' => $campus->name])] =
					$idSettings->getRoleCampusId(SchoolRoles::ParentRole(), $campus);
			
			if($idSettings->getRoleCampusId(SchoolRoles::EmployeeRole(), $campus)->preview)
				$both[__('people.id.both.employee', ['campus' => $campus->name])] =
					$idSettings->getRoleCampusId(SchoolRoles::EmployeeRole(), $campus);
		}
		$this->imports = $this->imports + $both;
		if(count($this->imports) > 0)
			$this->importName = array_keys($this->imports)[0];
		else
			$this->importName = "";
	}
	
	/**
	 * ASPECT RATIO
	 */
	
	public function updateAspectRatio(float $aspectRatio)
	{
		$this->idCard->aspectRatio = $aspectRatio;
		$this->isSaved = false;
	}
	
	/**
	 * CARD SETTINGS
	 */
	
	public function updateBackgroundColor(string $backgroundColor)
	{
		$this->idCard->backgroundColor = $backgroundColor;
		$this->isSaved = false;
	}
	
	public function updateBackgroundImageOpacity(float $opacity)
	{
		if($opacity < 0)
			$opacity = 0;
		if($opacity > 1)
			$opacity = 1;
		$this->idCard->backgroundImageOpacity = $opacity;
		$this->isSaved = false;
	}
	
	public function updateBackgroundBlendMode(string $blendMode)
	{
		$this->idCard->backgroundBlendMode = $blendMode;
		$this->isSaved = false;
	}
	
	public function updateBackgroundImage(?string $backgroundImage)
	{
		$this->idCard->backgroundImage = $backgroundImage;
		$this->isSaved = false;
	}
	
	public function updateRowsNumber(int $rowsNumber)
	{
		$this->idCard->updateRowCount($rowsNumber);
		$this->isSaved = false;
	}
	
	public function updateColumnsNumber(int $columnsNumber)
	{
		$this->idCard->updateColumnCount($columnsNumber);
		$this->isSaved = false;
	}
	
	public function updateContentPadding(int $padding)
	{
		$this->idCard->contentPadding = $padding;
		$this->isSaved = false;
	}
	
	/**
	 * SAVE
	 */
	
	public function save()
	{
		$this->originalCard = clone $this->idCard;
		$this->dispatch('id-card-saved', idCard: $this->originalCard);
		$this->isSaved = true;
	}
	
	public function revert()
	{
		$this->idCard = $this->originalCard;
		$this->isSaved = true;
	}
	
	public function clear()
	{
		$this->idCard = new IdCard();
		$this->isSaved = false;
	}
	
	public function import()
	{
		$this->idCard = clone $this->imports[$this->importName];
		$this->isSaved = false;
	}
	
	/**
	 * ELEMENT FUNCTIONS
	 */
	
	public function addElement(string $elementClass, int $row, int $col)
	{
		$element = new $elementClass();
		$this->idCard->addElement($element, $row, $col);
		$this->isSaved = false;
		$this->selectElement($row, $col);
	}
	
	public function selectElement(?int $elementRow, ?int $elementCol)
	{
		$this->selectedRow = $elementRow;
		$this->selectedCol = $elementCol;
		if($elementRow >= 0 && $elementCol >= 0 && $this->idCard->getContent($elementRow,
				$elementCol) instanceof IdCardElement)
			$this->selectedElement = $this->idCard->getContent($elementRow, $elementCol);
		else
			$this->selectedElement = null;
	}
	
	public function removeElement(int $elementRow, int $elementCol)
	{
		$this->idCard->removeElement($elementRow, $elementCol);
		$this->isSaved = false;
		$this->selectElement(-1, -1);
	}
	
	/**
	 * ELEMENT EXPANSION FUNCTIONS
	 */
	
	public function increaseColSpan(int $elementRow, int $elementCol)
	{
		if($this->idCard->getContent($elementRow, $elementCol) instanceof IdCardElement)
		{
			$newColspan = $this->idCard->getContent($elementRow, $elementCol)->colSpan + 1;
			$this->idCard->setElementColSpan($elementRow, $elementCol, $newColspan);
			$this->isSaved = false;
		}
	}
	
	public function decreaseColSpan(int $elementRow, int $elementCol)
	{
		if($this->idCard->getContent($elementRow, $elementCol) instanceof IdCardElement)
		{
			$newColspan = $this->idCard->getContent($elementRow, $elementCol)->colSpan - 1;
			$this->idCard->setElementColSpan($elementRow, $elementCol, $newColspan);
			$this->isSaved = false;
		}
	}
	
	public function increaseRowSpan(int $elementRow, int $elementCol)
	{
		if($this->idCard->getContent($elementRow, $elementCol) instanceof IdCardElement)
		{
			$newRowspan = $this->idCard->getContent($elementRow, $elementCol)->rowSpan + 1;
			$this->idCard->setElementRowSpan($elementRow, $elementCol, $newRowspan);
			$this->isSaved = false;
		}
	}
	
	public function decreaseRowSpan(int $elementRow, int $elementCol)
	{
		if($this->idCard->getContent($elementRow, $elementCol) instanceof IdCardElement)
		{
			$newRowspan = $this->idCard->getContent($elementRow, $elementCol)->rowSpan - 1;
			$this->idCard->setElementRowSpan($elementRow, $elementCol, $newRowspan);
			$this->isSaved = false;
		}
	}
	
	public function increaseRowSpanUp(int $elementRow, int $elementCol)
	{
		if($this->idCard->getContent($elementRow, $elementCol) instanceof IdCardElement)
		{
			$newRowspan = $this->idCard->getContent($elementRow, $elementCol)->rowSpan + 1;
			$this->idCard->moveElement($elementRow, $elementCol, ($elementRow - 1), $elementCol);
			$this->idCard->setElementRowSpan(($elementRow - 1), $elementCol, $newRowspan);
			$this->isSaved = false;
			$this->selectElement(($elementRow - 1), $elementCol);
		}
	}
	
	public function moveElement(int $elementRow, int $elementCol, int $newRow, int $newCol)
	{
		$this->idCard->moveElement($elementRow, $elementCol, $newRow, $newCol);
		$this->isSaved = false;
		//re-select the element
		$this->selectElement($newRow, $newCol);
	}
	
	public function decreaseRowSpanDown(int $elementRow, int $elementCol)
	{
		if($this->idCard->getContent($elementRow, $elementCol) instanceof IdCardElement)
		{
			$newRowspan = $this->idCard->getContent($elementRow, $elementCol)->rowSpan - 1;
			$this->idCard->moveElement($elementRow, $elementCol, ($elementRow + 1), $elementCol);
			$this->idCard->setElementRowSpan(($elementRow + 1), $elementCol, $newRowspan);
			$this->isSaved = false;
			$this->selectElement(($elementRow + 1), $elementCol);
		}
	}
	
	public function increaseColSpanLeft(int $elementRow, int $elementCol)
	{
		if($this->idCard->getContent($elementRow, $elementCol) instanceof IdCardElement)
		{
			$newColspan = $this->idCard->getContent($elementRow, $elementCol)->colSpan + 1;
			$this->idCard->moveElement($elementRow, $elementCol, $elementRow, ($elementCol - 1));
			$this->idCard->setElementColSpan($elementRow, ($elementCol - 1), $newColspan);
			$this->isSaved = false;
			$this->selectElement($elementRow, ($elementCol - 1));
		}
	}
	
	public function decreaseColSpanRight(int $elementRow, int $elementCol)
	{
		if($this->idCard->getContent($elementRow, $elementCol) instanceof IdCardElement)
		{
			$newColspan = $this->idCard->getContent($elementRow, $elementCol)->colSpan - 1;
			$this->idCard->moveElement($elementRow, $elementCol, $elementRow, ($elementCol + 1));
			$this->idCard->setElementColSpan($elementRow, ($elementCol + 1), $newColspan);
			$this->isSaved = false;
			$this->selectElement($elementRow, ($elementCol + 1));
		}
	}
	
	public function updateSetting(string $key, mixed $value)
	{
		if($this->selectedElement)
		{
			$this->idCard->getContent($this->selectedRow, $this->selectedCol)
			             ->setConfig($key, $value);
			$this->isSaved = false;
		}
	}
	
	public function viewPerson(Person $person)
	{
		$this->viewing = true;
		$this->viewingPerson = $person;
	}
	
	public function clearViewing()
	{
		$this->viewing = true;
		$this->viewingPerson = auth()->user();
	}
	
	public function render()
	{
		return view('livewire.people.id-creator.id-creator');
	}
}
