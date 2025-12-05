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
		if(count($this->tabs) < 2)
			$this->syncTabs();
		$tabId = $sessionSettings->get($session->id . '-selected-tab');
		if($tabId && isset($this->tabs[$tabId]))
			$this->selectTab($tabId);
		elseif(count($this->tabs) > 0)
			$this->selectTab(array_key_first($this->tabs));

		$this->availableWidgets =$this->getWidgets();
	}

	public function getWidgets()
	{
		$widgets = [];
		foreach($this->tabs as $tab)
			$widgets = array_merge($widgets, $tab->widgets);
		$availableWidgets = [];
		foreach((array)$this->classSession->classManager->service->data->widgets_allowed as $className => $widget)
		{
			$found = false;
			foreach($widgets as $w)
			{
				if($className == $w)
				{
					$found = true;
					break;
				}
			}
			if(!$found)
				$availableWidgets[$className] = $widget;
		}
		return $availableWidgets;
	}

	private function syncTabs(): void
	{
		$has_assignments = false;
		$has_messages = false;
		foreach($this->tabs as $tab)
		{
			if($tab->name == trans_choice('subjects.school.assignment', 2))
				$has_assignments = true;
			if($tab->name == trans_choice('subjects.school.message', 2))
				$has_messages = true;
		}
		if(!$has_assignments)
			$this->addAssignmentsTab();
		if(!$has_messages)
			$this->addMessagesTab();
	}

	private function addAssignmentsTab(): void
	{
		$assignmentTab = new ClassTab(trans_choice('subjects.school.assignment', 2));
		$assignmentTab->lock();
		$assignmentTab->containsClassLd = true;
		// add the assignments widget here
		$assignmentTab->addWidget(LearningDemonstrations::class);
		//add the tab to the list
		$this->tabs[$assignmentTab->getId()] = $assignmentTab;
	}

	private function addMessagesTab(): void
	{
		$messagesTab = new ClassTab(trans_choice('subjects.school.message', 2));
		$messagesTab->lock();
		$messagesTab->containsClassChat = true;
		$messagesTab->addWidget(ClassPageChat::class);
		//add the tab to the list
		$this->tabs[$messagesTab->getId()] = $messagesTab;
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

	public function addWidget(string $widgetClass)
	{
		$this->selectedTab->addWidget($widgetClass);
		$this->tabs[$this->selectedTab->getId()] = $this->selectedTab;
		$this->saveTabs();
		$this->availableWidgets =$this->getWidgets();
	}

    public function render()
    {
        return view('livewire.school.class-management.class-tabs');
    }
}
