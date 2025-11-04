<?php

namespace App\Livewire\SubjectMatter\Learning;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class LearningDemonstrationUrlEditor extends Component
{
	#[Modelable]
	public array $resources = [];
	
	public function addResource()
	{
		$this->resources[] = ['url' => '', 'title' => ''];
	}
	
	public function removeResource(int $pos)
	{
		unset($this->resources[$pos]);
		$this->resources = array_values($this->resources);
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
