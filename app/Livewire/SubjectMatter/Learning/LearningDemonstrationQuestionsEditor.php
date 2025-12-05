<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Classes\Learning\DemonstrationQuestion;
use App\Models\SubjectMatter\Learning\LearningDemonstration;
use App\Models\SubjectMatter\Learning\LearningDemonstrationTemplate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class LearningDemonstrationQuestionsEditor extends Component
{
	public LearningDemonstrationTemplate|LearningDemonstration $ld;
	#[Modelable]
	public array $questions = [];
	public bool $canUseAI = false;

	public function mount(LearningDemonstrationTemplate|LearningDemonstration $learningDemonstration)
	{
		$this->ld = $learningDemonstration;
		$this->canUseAI = ($learningDemonstration instanceof LearningDemonstrationTemplate) && auth()->user()->canUseAi();
		if(count($this->questions) == 0)
			$this->questions = array_map(fn($question) => $question->toArray(), $learningDemonstration->questions);
	}
	
	public function addQuestion()
	{
		$this->questions[] = (new DemonstrationQuestion())->toArray();
	}
	
	public function removeQuestion(int $pos)
	{
		unset($this->questions[$pos]);
		$this->questions = array_values($this->questions);
	}
	
	public function addAnswer(int $pos, string $answer)
	{
		$this->questions[$pos]['options'][] = $answer;
	}
	
	public function removeAnswer(int $pos, int $answerPos)
	{
		unset($this->questions[$pos]['options'][$answerPos]);
		$this->questions[$pos]['options'] = array_values($this->questions[$pos]['options']);
	}
	
    public function render()
    {
        return view('livewire.subject-matter.learning.learning-demonstration-questions-editor');
    }
}
