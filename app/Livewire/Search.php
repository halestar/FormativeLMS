<?php

namespace App\Livewire;

use App\Models\People\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Livewire\Component;

class Search extends Component
{
	public string $searchTerm = "";
	
	public function search()
	{
		$this->results = Person::where('first', 'like', '%' . $this->search . '%')
		                       ->orWhere('middle', 'like', '%' . $this->search . '%')
		                       ->orWhere('last', 'like', '%' . $this->search . '%')
		                       ->orWhere('nick', 'like', '%' . $this->search . '%')
		                       ->orWhere('email', 'like', '%' . $this->search . '%')
		                       ->get();
	}
	
	public function render()
	{
		if(strlen($this->searchTerm) >= 3)
		{
			$terms = preg_split('/[\s,]+/', strtolower($this->searchTerm));
			$results = Person::select('*');
			foreach($terms as $term)
			{
				$results = $results->where(function(Builder $query) use ($term)
				{
					$query->where('first', 'like', '%' . $term . '%')
					      ->orWhere('middle', 'like', '%' . $term . '%')
					      ->orWhere('last', 'like', '%' . $term . '%')
					      ->orWhere('nick', 'like', '%' . $term . '%')
					      ->orWhere('email', 'like', '%' . $term . '%');
				});
			}
			
			$results = $results->get();
		}
		else
		{
			$results = new Collection();
		}
		return view('livewire.search', ['results' => $results]);
	}
}
