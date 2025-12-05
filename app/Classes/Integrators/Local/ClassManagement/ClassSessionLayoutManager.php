<?php

namespace App\Classes\Integrators\Local\ClassManagement;

use App\Classes\Integrators\Local\ClassManagement\ClassWidget;
use App\Interfaces\Synthesizable;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassCommunicationObject;
use App\Models\SubjectMatter\Components\ClassStatus;

class ClassSessionLayoutManager implements Synthesizable
{
	public array $layout = [];
    protected ClassSession $owner;

    public function __construct(ClassSession $owner, array $layout = null)
    {
        $this->owner = $owner;
        $this->layout = $layout??  $owner->layout?? [];
    }
	
	public function getTopAnnouncement(): ClassStatus
	{
		if(!isset($this->layout['top_announcement_id']))
		{
            //try to load the object.
            $classStatus = ClassCommunicationObject::where('session_id', $this->owner->id)
                ->where('className', ClassStatus::class)
                ->first();
            if($classStatus)
            {
                //in this case, save the id of the object.
                $this->layout['top_announcement_id'] = $classStatus->id;
                $this->saveLayout();
                return $classStatus;
            }
			$classStatus = new ClassStatus();
            $classStatus->session_id = $this->owner->id;
            $classStatus->className = ClassStatus::class;
            $classStatus->posted_by = Auth()->user()->id;
			$classStatus->value = json_encode(ClassStatus::defaultValue());
            $classStatus->save();
            $this->layout['top_announcement_id'] = $classStatus->id;
            return $classStatus;
		}
		return ClassStatus::find($this->layout['top_announcement_id']);
	}
	
	public function saveLayout()
	{
        $this->owner->layout = $this->layout;
        $this->owner->save();
	}
	
	public function getTabs(): array
	{
		if(!isset($this->layout['tabs']))
			$this->layout['tabs'] = [];
		$tabs = [];
		if(count($this->layout['tabs']) > 0)
		{
			foreach($this->layout['tabs'] as $tab)
			{
				$t = ClassTab::hydrate($tab);
				$tabs[$t->getId()] = $t;
			}
		}
		return $tabs;
	}
	
	public function setTabs(array $tabs): void
	{
		$this->layout['tabs'] = [];
		foreach($tabs as $tab)
			$this->layout['tabs'][] = $tab->toArray();
		$this->saveLayout();
	}

    public function toArray(): array
    {
        return [ 'layout' => $this->layout, 'owner' => $this->owner->id];
    }

    public static function hydrate(array $data): static
    {
        $classSession = ClassSession::find($data['owner']);
        return new ClassSessionLayoutManager($classSession, $data['layout']);
    }
}
