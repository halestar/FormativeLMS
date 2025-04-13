<?php

namespace App\Classes\ClassManagement;

use Illuminate\Support\Str;

class ClassMessagesWidget extends ClassWidget
{

	public function getComponentName(): string
	{
		return "school.class-messages";
	}

	public static function hydrate(array $data): ClassWidget
	{
        $widget = new ClassMessagesWidget($data['id'], $data['order']);
        return $widget;
	}

	public static function getWidgetName(): string
	{
        return __('subjects.school.widgets.class-messages');
	}

	public static function create(int $order): ClassWidget
	{
        $id = Str::uuid();
        return new ClassMessagesWidget($id, $order);
	}

	public function deleteWidget(): void
	{
		//nothing to do here, since all the data is persisted.
	}
}
