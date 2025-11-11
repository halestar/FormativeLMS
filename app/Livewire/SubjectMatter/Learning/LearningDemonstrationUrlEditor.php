<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Models\SubjectMatter\Learning\LearningDemonstrationTemplate;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class LearningDemonstrationUrlEditor extends Component
{
	#[Modelable]
	public array $resources = [];
	public bool $canUseAI = false;
	public LearningDemonstrationTemplate $ld;

	public function mount(LearningDemonstrationTemplate $learningDemonstration)
	{
		// Do we have system AI permissions?
		$this->canUseAI = auth()->user()->canUseAi();
		$this->ld = $learningDemonstration;
		if(count($this->resources) == 0)
			$this->resources = array_map(fn($link) => $link->toArray(), $this->ld->links);
	}
	
	public function addResource()
	{
		$this->resources[] = ['url' => '', 'title' => ''];
	}
	
	public function removeResource(int $pos)
	{
		unset($this->resources[$pos]);
		$this->resources = count($this->resources) == 0? []: array_values($this->resources);
	}
	
	public function updateUrl(int $pos, string $url)
	{
		$this->resources[$pos]['url'] = $url;
	}
	
	public function updateTitle(int $pos, string $title)
	{
		$this->resources[$pos]['title'] = $title;
	}
	
    public function render()
    {
        return view('livewire.subject-matter.learning.learning-demonstration-url-editor');
    }
}
