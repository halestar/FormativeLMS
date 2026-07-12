<?php

use App\Models\People\Person;
use Livewire\Component;

new class extends Component
{
	public Person $person;

	public function mount()
	{
		$this->person = auth()->user();
	}


};
?>

<div>
    {{-- Simplicity is an acquired taste. - Katharine Gerould --}}
</div>