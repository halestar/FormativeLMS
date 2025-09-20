<?php

namespace App\Livewire\Locations;

use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use Illuminate\Support\Collection;
use Livewire\Component;

class TermEditor extends Component
{
	
	public Campus $campus;
	public Year $year;
	public Collection $terms;
	
	public ?string $label = null;
	public string $term_start;
	public string $term_end;
	public bool $adding = false;
	public ?int $editing = null;
	
	public function messages(): array
	{
		return [
			'label' => __('errors.terms.label'),
			'campus_id' => __('errors.terms.campus_id'),
			'term_start' => __('errors.terms.start',
				[
					'start' => $this->year->year_start->format(config('lms.date_format')),
					'end' => $this->year->year_end->format(config('lms.date_format')),
				]),
			'term_end' => __('errors.terms.end',
				[
					'start' => $this->year->year_start->format(config('lms.date_format')),
					'end' => $this->year->year_end->format(config('lms.date_format')),
				]),
		];
	}
	
	public function rules()
	{
		return [
			'label' => 'required|max:255',
			'term_start' => 'required|date|after_or_equal:' . $this->year->year_start . '|before_or_equal:term_end',
			'term_end' => 'required|date|after_or_equal:term_start|before_or_equal:' . $this->year->year_end,
		];
	}
	
	public function mount(Campus $campus, Year $year): void
	{
		$this->campus = $campus;
		$this->year = $year;
		$this->terms = $year->campusTerms($campus)
		                    ->get();
		$this->term_start = $this->year->year_start->format('Y-m-d');
		$this->term_end = $this->year->year_end->format('Y-m-d');
	}
	
	public function addTerm(): void
	{
		$this->authorize('has-permission', 'locations.terms');
		$this->validate();
		$term = new Term();
		$term->label = $this->label;
		$term->term_start = $this->term_start;
		$term->term_end = $this->term_end;
		$term->campus_id = $this->campus->id;
		$term->year_id = $this->year->id;
		$term->save();
		$this->clearForm();
	}
	
	public function clearForm(): void
	{
		$this->label = null;
		$this->term_start = $this->year->year_start->format('Y-m-d');
		$this->term_end = $this->year->year_end->format('Y-m-d');
		$this->adding = false;
		$this->editing = null;
		$this->terms = $this->year->campusTerms($this->campus)
		                          ->get();
	}
	
	public function loadEdit(Term $term): void
	{
		$this->editing = $term->id;
		$this->label = $term->label;
		$this->term_start = $term->term_start->format('Y-m-d');
		$this->term_end = $term->term_end->format('Y-m-d');
	}
	
	public function updateTerm(): void
	{
		$this->authorize('has-permission', 'locations.terms');
		$term = Term::find($this->editing);
		$term->label = $this->label;
		$term->term_start = $this->term_start;
		$term->term_end = $this->term_end;
		$term->save();
		$this->clearForm();
	}
	
	public function deleteTerm(Term $term): void
	{
		$this->authorize('has-permission', 'locations.terms');
		if($term->canDelete())
			$term->delete();
		$this->clearForm();
	}
	
	public function render()
	{
		return view('livewire.locations.term-editor');
	}
}
