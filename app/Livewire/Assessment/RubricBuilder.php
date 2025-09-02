<?php

namespace App\Livewire\Assessment;

use App\Casts\Rubric;
use App\Interfaces\HasRubric;
use Livewire\Attributes\Validate;
use Livewire\Component;

class RubricBuilder extends Component
{
    public HasRubric $skill;
    public Rubric $rubric;
    public bool $updating = false;
    public ?string $updateType = null;
    public int $updateColumn = 0;
    public int $updateRow = 0;
    public string $updateValue = '';
    public float $updateScoreValue = 0;
    #[Validate]
    public float $newScore;
    public bool $saved;

    private function saveLocally()
    {
        $user = auth()->user();
        $user->prefs->set('rubric-builder.' . $this->skill->getSkillId(), $this->rubric);
        $user->save();
        $this->saved = false;
    }

    private function clearLocalSave()
    {
        $user = auth()->user();
        $user->prefs->set('rubric-builder.' . $this->skill->getSkillId(), null);
        $user->save();
        $this->saved = true;
    }


    protected function rules()
    {
        return [
            'newScore' =>
                [
                    'required',
                    'decimal:0,2',
                    'min:0',
                ],
        ];
    }

    public function mount(HasRubric $skill)
    {
        $this->skill = $skill;
        $rubric = auth()->user()->prefs->get('rubric-builder.' . $this->skill->getSkillId(), null);
        if($rubric)
        {
            $rubric = Rubric::hydrate($rubric);
            $this->rubric = $rubric;
            $this->saved = false;
        }
        elseif($this->skill->getRubric())
        {
            $this->rubric = $skill->getRubric();
            $this->saved = true;
        }
        else
        {
            $this->rubric = new Rubric();
            $this->saved = false;
        }
        $this->newScore = (isset($this->rubric->points) && count($this->rubric->points) > 0)?
	        $this->rubric->points[(count($this->rubric->points) - 1)] + 1 : 1;
    }

    public function save()
    {
        $this->skill->setRubric($this->rubric);
        $this->skill->save();
        $this->clearLocalSave();
    }

    public function discardChanges()
    {
        $this->rubric = $this->skill->getRubric()?? new Rubric();
        $this->clearLocalSave();
    }

    public function addCriteria()
    {
        $this->rubric->addCriteria();
        $this->saveLocally();
    }

    public function setUpdateCriteria(int $row)
    {
        $this->updating = true;
        $this->updateType = 'criteria';
        $this->updateColumn = 0;
        $this->updateRow = $row;
        $this->updateValue = $this->rubric->criteria[$row];
    }

    public function updateCriteria()
    {
        $this->rubric->criteria[$this->updateRow] = $this->updateValue;
        $this->clearEdit();
        $this->saveLocally();
    }

    public function removeCriteria(int $row)
    {
        $this->rubric->removeCriteria($row);
        $this->saveLocally();
    }

    public function setUpdateDescription(int $row, int $column)
    {
        $this->updating = true;
        $this->updateType = 'descriptions';
        $this->updateColumn = $column;
        $this->updateRow = $row;
        $this->updateValue = $this->rubric->descriptions[$row][$column];
    }

    public function updateDescription()
    {
        $this->rubric->descriptions[$this->updateRow][$this->updateColumn] = $this->updateValue;
        $this->clearEdit();
        $this->saveLocally();
    }

    public function addScore()
    {
        //first, check the ranges
        $this->validate();
        $this->rubric->addPoint($this->newScore);
        $this->clearEdit();
        $this->saveLocally();
    }

    public function setUpdateScore(int $col)
    {
        $this->updating = true;
        $this->updateType = 'points';
        $this->updateColumn = $col;
        $this->updateRow = 0;
        $this->updateScoreValue = $this->rubric->points[$col];
    }

    public function updateScore()
    {
        $this->rubric->updateScore($this->rubric->points[$this->updateColumn], $this->updateScoreValue);
        $this->clearEdit();
        $this->saveLocally();
    }

    public function removeScore(float $score)
    {
        $this->rubric->removePoint($score);
        $this->clearEdit();
        $this->saveLocally();
    }

    public function clearEdit()
    {
        $this->updating = false;
        $this->updateType = null;
        $this->updateRow = 0;
        $this->updateColumn = 0;
        $this->newScore = (isset($this->rubric->points) && count($this->rubric->points) > 0)?
            $this->rubric->points[0] + 1: 1;
        $this->updateScoreValue = 0;
    }

    public function updateCriteriaOrder($models)
    {
        $newOrder = [];
        foreach ($models as $model)
            $newOrder[($model['order'] - 1)] = $model['value'];
        $this->rubric->reorderCriteria($newOrder);
        $this->saveLocally();
    }
	
	public function clearRubric()
	{
		$this->rubric = new Rubric();
		$this->saved = false;
	}

    public function render()
    {
        return view('livewire.assessment.rubric-builder');
    }
}
