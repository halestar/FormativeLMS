<?php

namespace App\Livewire\School\ClassManagement;

use App\Classes\Integrators\Local\ClassManagement\ClassSessionLayoutManager;
use App\Classes\Integrators\Local\ClassManagement\ClassTab;
use App\Classes\SessionSettings;
use App\Enums\ClassViewer;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ClassTabs extends Component
{
	public ClassSession $classSession;
	public Person $self;
	public ClassSessionLayoutManager $layout;
	public array $tabs;
	public bool $editing = false;
	public bool $canManage = false;
	public ?ClassTab $selectedTab = null;
	public string $classes = '';
	public string $style = '';
	#[Validate('required|min:3')]
	public string $tabName = '';
	public array $availableWidgets = [];

	public function mount(ClassSession $session, SessionSettings $sessionSettings)
	{
		$this->classSession = $session;
		$this->canManage = $this->classSession->viewingAs( ClassViewer::ADMIN) || $this->classSession->viewingAs( ClassViewer::FACULTY);
		$this->self = auth()->user();
		$this->layout = $session->classManager->getClassLayoutManager($session);
		$this->tabs = $this->layout->getTabs();
		$tabId = $sessionSettings->get($session->id . '-selected-tab');
		if($tabId && isset($this->tabs[$tabId]))
			$this->selectTab($tabId);
		elseif(count($this->tabs) > 0)
			$this->selectTab(array_key_first($this->tabs));

		$this->availableWidgets = $this->getWidgets();
	}

	public function getWidgets()
	{
		$availableWidgets = [];
		foreach((array)$this->classSession->classManager->service->data->optional as $widget)
			$availableWidgets[$widget] = $this->classSession->classManager->service->data->available->$widget;
		return $availableWidgets;
	}

	public function getListeners()
	{
		return
			[
				"class-page-set-editing" => 'setEditing',
			];
	}

	public function setEditing(bool $editing)
	{
		$this->editing = $editing;
	}

	public function selectTab(string $tabId)
	{
		$sessionSettings = app(SessionSettings::class);
		$this->selectedTab = $this->tabs[$tabId];
		$this->tabName = $this->selectedTab->name;
		$sessionSettings->set($this->classSession->id . '-selected-tab', $tabId);
	}

	public function updateTabOrder($models)
	{
		$tabs = [];
		foreach($models as $model)
		{
			$tab = $this->tabs[$model['value']] ?? null;
			if($tab)
				$tabs[$tab->getId()] = $tab;
		}
		$this->tabs = $tabs;
		$this->saveTabs();
	}

	public function addTab()
	{
		if($this->canManage)
		{
			$newTab = new ClassTab(__('subjects.school.tabs.new'));
			$this->tabs[$newTab->getId()] = $newTab;
			$this->saveTabs();
			$this->selectTab($newTab->getId());
		}
	}

	private function saveTabs(): void
	{
		$this->layout->setTabs($this->tabs);
		$this->dispatch('class-page-layout-updated');
	}

	public function deleteTab()
	{
		unset($this->tabs[$this->selectedTab->getId()]);
		$this->selectTab($this->tabs[array_key_first($this->tabs)]->getId());
		$this->saveTabs();
	}

	public function updateTab(): void
	{
		$this->validate();
		$this->tabs[$this->selectedTab->getId()]->name = $this->tabName;
		$this->selectTab($this->selectedTab->getId());
		$this->saveTabs();
	}

	public function setWidget(string $widget)
	{
		$this->selectedTab->setWidget($widget);
		$this->tabs[$this->selectedTab->getId()] = $this->selectedTab;
		$this->saveTabs();
		$this->availableWidgets = $this->getWidgets();
	}

    public function render()
    {
        return view('livewire.school.class-management.class-tabs');
    }
}
