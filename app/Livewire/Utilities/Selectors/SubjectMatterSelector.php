<?php

namespace App\Livewire\Utilities\Selectors;

use App\Models\Locations\Campus;
use App\Models\SubjectMatter\Subject;
use Illuminate\Support\Collection;
use Livewire\Component;

class SubjectMatterSelector extends Component
{
	public string $classes = '';
	public Collection $campuses;
	public Collection $subjects;
	public ?Campus $selectedCampus;
	public int $selectedCampusId;
	public ?Subject $selectedSubject;
	public int $selectedSubjectId;
	
	public function mount(string $classes = '', Campus|Subject|null $openTo = null)
	{
		$this->classes = $classes;
		$this->campuses = Campus::all();
		
		if($this->campuses->count() > 0)
		{
			if($openTo instanceof Campus)
				$this->selectedCampusId = $openTo->id;
			elseif($openTo instanceof Subject)
				$this->selectedCampusId = $openTo->campus_id;
			else
				$this->selectedCampusId = $this->campuses->first()->id;
			$this->setCampus($openTo);
		}
	}
	
	public function setCampus(Campus|Subject|null $openTo = null)
	{
		$campus = $this->campuses->where('id', $this->selectedCampusId)
		                         ->first();
		if($campus)
		{
			$this->selectedCampus = $campus;
			$this->subjects = $campus->subjects;
			if($openTo instanceof Subject)
			{
				$this->selectedSubject = $openTo;
				$this->selectedSubjectId = $openTo->id;
			}
			else
			{
				$this->selectedSubject = $this->subjects->first();
				if($this->selectedSubject)
					$this->selectedSubjectId = $this->selectedSubject->id;
			}
		}
	}
	
	public function render()
	{
		return view('livewire.utilities.selectors.subject-matter-selector');
	}
}
