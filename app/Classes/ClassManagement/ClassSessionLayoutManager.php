<?php

namespace App\Classes\ClassManagement;

use App\Models\SubjectMatter\ClassSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class ClassSessionLayoutManager
{
	public ?array $layout;
	public ClassSession $owner;
	public Collection $availableWidgets;
	
	public function __construct(?string $layout, ClassSession $owner)
	{
		$this->layout = json_decode($layout, true);
		$this->owner = $owner;
		$this->availableWidgets = collect();
	}
	
	public function canManage()
	{
		return Gate::allows('manage', $this->owner);
	}
	
	public function getTopAnnouncement(): TopAnnouncementWidget
	{
		if(!isset($this->layout['top_announcement']))
		{
			$this->layout['top_announcement'] =
				[
					'announcement' => null,
					'color' => '#ffffff',
					'expiry' => null,
				];
			$this->saveLayout();
		}
		return new TopAnnouncementWidget($this->layout['top_announcement'], $this);
	}
	
	public function saveLayout()
	{
		$this->owner->layout = $this->layout;
		$this->owner->save();
	}
	
	public function setTopAnnouncement(TopAnnouncementWidget $announcement): void
	{
		$this->layout['top_announcement'] =
			[
				'announcement' => $announcement->getAnnouncement(),
				'color' => $announcement->getAnnouncementColor(),
				'expiry' => $announcement->getAnnouncementExpiry(),
			];
		$this->saveLayout();
	}
	
	public function getTabs(): ClassTabs
	{
		if(!isset($this->layout['tabs']))
			$this->layout['tabs'] = (new ClassTabs())->toArray();
		return ClassTabs::hydrate($this->layout['tabs']);
	}
	
	public function setTabs(ClassTabs $tabs): void
	{
		$this->layout['tabs'] = $tabs->toArray();
		$this->saveLayout();
	}
	
	public function updateWidget(ClassWidget $widget): void
	{
		//we need to search the tabs
		$found = false;
		for($i = 0; $i < count($this->layout['tabs']); $i++)
		{
			if(isset($this->layout['tabs'][$i]['widgets']) && count($this->layout['tabs'][$i]['widgets']) > 0)
			{
				for($j = 0; $j < count($this->layout['tabs'][$i]['widgets']); $j++)
				{
					if($this->layout['tabs'][$i]['widgets'][$j]['id'] == $widget->getId())
					{
						$this->layout['tabs'][$i]['widgets'][$j] = $widget->toArray();
						$found = true;
						break;
					}
				}
			}
			if($found)
				break;
		}
		$this->saveLayout();
	}
	
	public function hasWidget(ClassWidget $widget): bool
	{
		for($i = 0; $i < count($this->layout['tabs']); $i++)
		{
			if(isset($this->layout['tabs'][$i]['widgets']) && count($this->layout['tabs'][$i]['widgets']) > 0)
			{
				for($j = 0; $j < count($this->layout['tabs'][$i]['widgets']); $j++)
				{
					if($this->layout['tabs'][$i]['widgets'][$j]['id'] == $widget->getId())
						return true;
				}
			}
		}
		return false;
	}
	
	public function getWidget(ClassWidget $widget): ?ClassWidget
	{
		for($i = 0; $i < count($this->layout['tabs']); $i++)
		{
			if(isset($this->layout['tabs'][$i]['widgets']) && count($this->layout['tabs'][$i]['widgets']) > 0)
			{
				for($j = 0; $j < count($this->layout['tabs'][$i]['widgets']); $j++)
				{
					if($this->layout['tabs'][$i]['widgets'][$j]['id'] == $widget->getId())
						return $this->layout['tabs'][$i]['widgets'][$j];
				}
			}
		}
		return null;
	}
	
	public function getWidgetTypes(string $className): array
	{
		$widgets = [];
		for($i = 0; $i < count($this->layout['tabs']); $i++)
		{
			if(isset($this->layout['tabs'][$i]['widgets']) && count($this->layout['tabs'][$i]['widgets']) > 0)
			{
				for($j = 0; $j < count($this->layout['tabs'][$i]['widgets']); $j++)
				{
					if($this->layout['tabs'][$i]['widgets'][$j]['className'] == $className)
						$widgets[] = ($className)::hydrate($this->layout['tabs'][$i]['widgets'][$j]);
				}
			}
		}
		return $widgets;
	}
	
}
