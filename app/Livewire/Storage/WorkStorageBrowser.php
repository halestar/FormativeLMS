<?php

namespace App\Livewire\Storage;

use App\Classes\Storage\Work\WorkStorage;
use App\Interfaces\Fileable;
use Livewire\Component;

class WorkStorageBrowser extends Component
{
	public string $title;
	public WorkStorage $workStorage;
	public Fileable $fileable;
	public array $workFiles;
	
	public function mount(WorkStorage $workStorage, Fileable $fileable)
	{
		$this->title = __('storage.work.browser');
		$this->workStorage = $workStorage;
		$this->fileable = $fileable;
		$this->workFiles = $this->fileable->workFiles;
	}
	
	public function render()
	{
		return view('livewire.storage.work-storage-browser');
	}
}
