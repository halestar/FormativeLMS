<?php

namespace App\Classes\ClassManagement;

use App\Models\SubjectMatter\ClassSession;
use App\Models\Utilities\DataPayload;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

abstract class ClassWidget
{
	protected string $id;
	protected int $order;
	protected ?DataPayload $data = null;
	
	protected function __construct(string $id, int $order = 0)
	{
		$this->id = $id;
		$this->order = $order;
	}
	
	public static function sessionsWithWidgets(): Collection
	{
		return ClassSession::select('class_sessions.*')
		                   ->join('class_sessions_teachers', 'class_sessions_teachers.session_id', '=',
			                   'class_sessions.id')
		                   ->where('class_sessions_teachers.person_id', Auth::user()->id)
		                   ->whereRaw("JSON_CONTAINS(layout->'$.tabs[*].widgets[*].className', '\"" . str_replace('\\',
				                   '\\\\\\\\', get_called_class()) . "\"')")
		                   ->get();
	}
	
	public abstract static function hydrate(array $data): ClassWidget;
	
	public static abstract function getWidgetName(): string;
	
	public static abstract function create(int $order): ClassWidget;
	
	public function getId(): string
	{
		return $this->id;
	}
	
	public function getOrder(): int
	{
		return $this->order;
	}
	
	public function setOrder(int $order): void
	{
		$this->order = $order;
	}
	
	public function toArray(): array
	{
		return
			[
				'id' => $this->id,
				'order' => $this->order,
				'className' => get_class($this),
			];
	}
	
	public function saveWidget(): void
	{
		//first, we get the session that this belongs to.
		$session = $this->getClassSession();
		if($session)
		{
			$layout = $session->layout;
			$layout->updateWidget($this);
		}
	}
	
	public function getClassSession(): ?ClassSession
	{
		return ClassSession::whereRaw('JSON_SEARCH(layout, "ONE", "' . $this->id . '", null,"$.tabs[*].widgets[*].id")')
		                   ->first();
	}
	
	public abstract function getComponentName(): string;
	
	public abstract function deleteWidget(): void;
	
	protected function getData(): array
	{
		if(!$this->data)
		{
			//first we try to find it
			$this->data = DataPayload::find($this->id);
			if(!$this->data)
			{
				//in this case, we need to create it
				$this->data = new DataPayload();
				$this->data->id = $this->id;
				$this->data->payload = [];
				$this->data->save();
			}
		}
		return $this->data->payload;
	}
	
	protected function setData(array $data): void
	{
		if(!$this->data)
			$this->getData();
		$this->data->payload = $data;
		$this->data->save();
	}
	
	
}
