<?php

namespace App\Classes\Synths;

use App\Classes\ClassManagement\ClassSessionLayoutManager;
use App\Classes\ClassManagement\TopAnnouncementWidget;
use App\Models\SubjectMatter\ClassSession;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;

class TopAnnouncementWidgetSynth extends Synth
{
	
	public static $key = "topAnnouncementWidget";
	
	public static function match($target)
	{
		return $target instanceof TopAnnouncementWidget;
	}
	
	public function dehydrate($target)
	{
		return [[
			        'announcement' => $target->announcement,
			        'color' => $target->color,
			        'expiry' => $target->expiry->format('Y-m-d H:i:s'),
			        'owner_layout' => json_encode($target->owner->layout),
			        'owner_id' => $target->owner->owner->id,
		        ], []];
	}
	
	public function hydrate($value): TopAnnouncementWidget
	{
		$classSession = ClassSession::find($value['owner_id']);
		$layout = new ClassSessionLayoutManager($value['owner_layout'], $classSession);
		
		$data =
			[
				'announcement' => $value['announcement'],
				'color' => $value['color'],
				'expiry' => $value['expiry'],
			];
		return new TopAnnouncementWidget($data, $layout);
	}
}
