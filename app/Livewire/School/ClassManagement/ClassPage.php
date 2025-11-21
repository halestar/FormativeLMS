<?php

namespace App\Livewire\School\ClassManagement;

use App\Classes\ClassManagement\ClassSessionLayoutManager;
use App\Classes\ClassManagement\ClassTab;
use App\Classes\ClassManagement\ClassTabs;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ClassPage extends Component
{
	public ClassSession $classSession;
	public ClassSessionLayoutManager $layout;
	public ClassTabs $tabs;
	
	public bool $editing = false;
	public bool $canManage;
	public ?string $tabName = null;
	
	public ?string $editTabName = null;
	public ?ClassTab $selectedTab;
	public array $widgets;
	
	public function mount(ClassSession $classSession)
	{
		$this->classSession = $classSession;
		$this->layout = $classSession->layout;
		$this->tabs = $this->layout->getTabs();
		$this->canManage = Gate::allows('manage', $this->classSession);
		$this->selectedTab = $this->tabs->tabs[0];
		$this->editTabName = $this->selectedTab->name;
		$this->widgets = $this->selectedTab ? $this->selectedTab->widgets : [];
	}
	
	public function setEdit(bool $editing)
	{
		$this->editing = $editing;
	}
	
	public function addTab()
	{
		Validator::make(
		// Data to validate...
			['tabName' => $this->tabName],
			
			// Validation rules to apply...
			['tabName' => 'required|min:3'],
			
			// Custom validation messages...
			['tabName' => __('errors.tab.name')],
		)
		         ->validate();
		if($this->canManage)
		{
			$tab = new ClassTab($this->tabName);
			$this->tabs->addTab($tab);
			$this->selectTab($tab->getId());
		}
		$this->saveTabs();
		$this->dispatch('close-add-tabs');
	}
	
	public function selectTab(string $tabId)
	{
		//find trhe tab
		$this->selectedTab = $this->tabs->getTab($tabId);
		$this->editTabName = $this->selectedTab->name;
		$this->widgets = $this->selectedTab ? $this->selectedTab->widgets : [];
	}
	
	/**
	 * TAB FUNCTIONS
	 */
	private function saveTabs()
	{
		$this->layout->setTabs($this->tabs);
		$this->tabs = $this->layout->getTabs();
	}
	
	public function deleteTab(string $tabId)
	{
		$this->tabs->removeTab($tabId);
		$this->selectTab($this->tabs->tabs[0]->getId());
		$this->saveTabs();
	}
	
	public function updateTabOrder($models)
	{
		$ids = [];
		foreach($models as $model)
			$ids[] = $model['value'];
		$this->tabs->reorderTabs($ids);
		$this->saveTabs();
	}
	
	public function addWidget(string $widgetClass): void
	{
		$widget = ($widgetClass)::create(count($this->selectedTab->widgets));
		$this->selectedTab->addWidget($widget);
		$this->saveWidgets();
	}
	
	/**
	 * WIDGET FUNCTIONS
	 */
	private function saveWidgets()
	{
		//all new widgets are saved to the selected tab, so we need to update this first
		$this->tabs->updateTab($this->selectedTab);
		//and we save the tab
		$this->saveTabs();
		$this->widgets = $this->selectedTab->widgets;
	}
	
	public function updateTab(): void
	{
		Validator::make(
		// Data to validate...
			['editTabName' => $this->editTabName],
			
			// Validation rules to apply...
			['editTabName' => 'required|min:3'],
			
			// Custom validation messages...
			['editTabName' => __('errors.tab.name')],
		)
		         ->validate();
		$this->selectedTab->name = $this->editTabName;
		$this->tabs->updateTab($this->selectedTab);
		$this->selectedTab = $this->tabs->getTab($this->selectedTab->getId());
		$this->saveTabs();
	}
	
	public function deleteWidget(string $widgetId): void
	{
		$widget = $this->selectedTab->removeWidget($widgetId);
		$widget->deleteWidget();
		$this->saveWidgets();
	}
	
	public function render()
	{
		return view('livewire.school.class-page');
	}
}
