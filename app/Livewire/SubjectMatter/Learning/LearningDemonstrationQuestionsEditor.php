<?php

namespace App\Livewire\SubjectMatter\Learning;

use App\Classes\Learning\DemonstrationQuestion;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Modelable;
use Livewire\Component;

class LearningDemonstrationQuestionsEditor extends Component
{
	#[Modelable]
	public array $questions = [];
	
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
		Log::debug('addAnswer', ['pos' => $pos, 'answer' => $answer]);
		Log::debug("options is now: " . print_r($this->questions[$pos], true));
		$this->questions[$pos]['options'][] = $answer;
		Log::debug("options is now: " . print_r($this->questions[$pos], true));
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
