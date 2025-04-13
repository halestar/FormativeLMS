<?php

namespace App\Classes\ClassManagement;

class ClassTabs
{
    public array $tabs;

    private function addAssignmentsTab():void
    {
        $assignmentTab = new ClassTab(trans_choice('subjects.school.assignment', 2));
        $assignmentTab->lock();
        // add the assignments widget here
        $this->tabs[] = $assignmentTab;
    }

    private function addMessagesTab():void
    {
        $messagesTab = new ClassTab(trans_choice('subjects.school.message', 2));
        $messagesTab->lock();
        $messagesTab->addWidget(ClassMessagesWidget::create(0));
        $this->tabs[] = $messagesTab;
    }

    public function syncTabs(): void
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

    public function __construct(array $tabs = [])
    {
        $this->tabs = $tabs;
        if(count($this->tabs) < 2)
            $this->syncTabs();
    }

    public function getTabs(): array
    {
        return $this->tabs;
    }

    public function addTab(ClassTab $tab): void
    {
        $this->tabs[] = $tab;
    }

    public function removeTab(string $tabId): void
    {
        $newTabs = [];
        foreach($this->tabs as $tab)
        {
            if($tab->getId() == $tabId && !$tab->isLocked())
                continue;
            $newTabs[] = $tab;
        }
        $this->tabs = $newTabs;
    }

    public function toArray(): array
    {
        $tabs = [];
        foreach($this->tabs as $tab)
            $tabs[] = $tab->toArray();
        return $tabs;
    }

    public static function hydrate(array $data): ClassTabs
    {
        $tabs = [];
        foreach($data as $tab)
            $tabs[] = ClassTab::hydrate($tab);
        return new ClassTabs($tabs);
    }

    public function getTab(string $tabId): ?ClassTab
    {
        foreach($this->tabs as $tab)
            if($tab->getId() == $tabId)
                return $tab;
        return null;
    }

    public function updateTab(ClassTab $tab): void
    {
        for($i = 0; $i < count($this->tabs); $i++)
            if($this->tabs[$i]->getId() == $tab->getId())
                $this->tabs[$i] = $tab;
    }

    public function reorderTabs(array $tabIds): void
    {
        $tabs = [];
        foreach($tabIds as $tabId)
        {
            $tab = $this->getTab($tabId);
            if($tab)
                $tabs[] = $tab;
        }
        $this->tabs = $tabs;
    }


}
