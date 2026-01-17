<?php

namespace App\Classes\Integrators\Local\ClassManagement;

use App\Classes\Integrators\Local\LocalIntegrator;
use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\DocumentFile;
use App\Enums\IntegratorServiceTypes;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\Synthesizable;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassCommunicationObject;
use App\Models\SubjectMatter\Components\ClassStatus;
use App\Models\Utilities\WorkFile;

class ClassSessionLayoutManager implements Synthesizable
{
	public array $layout = [];
    protected ClassSession $owner;
	public const CLASS_IMG_FNAME = "class_image";

    public function __construct(ClassSession $owner, array $layout = null)
    {
        $this->owner = $owner;
        $this->layout = $layout??  $owner->layout?? [];
		if(count($this->layout) == 0)
			$this->buildLayout();
    }

	private function buildTopAnnouncement(): void
	{
		//try to load the object.
		$classStatus = ClassCommunicationObject::where('session_id', $this->owner->id)
			->where('className', ClassStatus::class)
			->first();
		//if there is an object already, we'll just make sure it's saved.
		if($classStatus)
			$this->layout['top_announcement_id'] = $classStatus->id;
		else
		{
			//else we'll have to build it.
			$classStatus = new ClassStatus();
			$classStatus->session_id = $this->owner->id;
			$classStatus->className = ClassStatus::class;
			$classStatus->posted_by = Auth()->user()->id;
			$classStatus->value = json_encode(ClassStatus::defaultValue());
			$classStatus->save();
			$this->layout['top_announcement_id'] = $classStatus->id;
		}
	}

	private function buildTabs(): void
	{
		//We create the tabs by creating a tab for every widget that is required
		$classesService = LocalIntegrator::getService(IntegratorServiceTypes::CLASSES);
		$tabs = [];
		foreach($classesService->data->required as $widget)
		{
			$tab = new ClassTab($classesService->data->available->$widget);
			$tab->setWidget($widget);
			$tabs[] = $tab->toArray();
		}
		$this->layout['tabs'] = $tabs;
	}

	private function buildLayout(): void
	{
		//there is no layout set, so we build the default.
		$this->layout = [];
		// the class image is the campus image
		$this->layout['class_img'] = $this->owner->course->campus->img->url;
		//generate the top announcement
		$this->buildTopAnnouncement();
		//next, we need to generate the tabs
		$this->buildTabs();
		$this->saveLayout();
	}

	public function verifyLayout(): void
	{
		//check if the top announcement is set.
		if(!$this->layout['top_announcement_id'])
			$this->buildTopAnnouncement();
		//check if the class image is set.
		if(!$this->layout['class_img'])
			$this->layout['class_img'] = $this->owner->course->campus->img->url;
		//verify the tabs
		$classesService = LocalIntegrator::getService(IntegratorServiceTypes::CLASSES);
		$existingTabs = $this->getTabs();
		foreach($classesService->data->required as $widget)
		{
			// is there a tab for this widget?
			$found = false;
			foreach($existingTabs as $tab)
			{
				if($tab->widget == $widget)
				{
					$found = true;
					break;
				}
			}
			if(!$found)
			{
				//add the tab.
				$tab = new ClassTab($classesService->data->available->$widget);
				$tab->setWidget($widget);
				$this->layout['tabs'][] = $tab->toArray();
			}
		}
		//save the layout.
		$this->saveLayout();
	}
	
	public function getTopAnnouncement(): ClassStatus
	{
		if(!isset($this->layout['top_announcement_id']))
		{
			$this->buildTopAnnouncement();
			$this->saveLayout();
		}
		return ClassStatus::find($this->layout['top_announcement_id']);
	}
	
	public function saveLayout()
	{
        $this->owner->layout = $this->layout;
        $this->owner->save();
	}

	public function getClassImage(): string
	{
		return $this->layout['class_img'];
	}

	public function setClassImage(string $img): void
	{
		$this->layout['class_img'] = $img;
		$this->saveLayout();
	}

	public function getClassImageFile(): ?WorkFile
	{
		return $this->owner->workFiles()->where('name', self::CLASS_IMG_FNAME)->first();
	}

	public function updateClassImageFile(DocumentFile $file): void
	{
		$storageSettings = app(StorageSettings::class);
		//if we have an existing file, we delete it.
		$this->getClassImageFile()?->delete();
		//change the name of the file, but do't save it.
		$file->name = self::CLASS_IMG_FNAME;
		//get the connection and persist the file.
		$connection = $storageSettings->getWorkConnection(WorkStoragesInstances::ClassWork);
		$imgFile = $connection->persistFile($this->owner, $file, false);
		//save the path.
		$this->setClassImage($imgFile->url);
	}
	
	public function getTabs(): array
	{
		if(!isset($this->layout['tabs']))
		{
			$this->buildTabs();
			$this->saveLayout();
		}
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
