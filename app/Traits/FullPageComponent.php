<?php

namespace App\Traits;

use Livewire\Attributes\Locked;

trait FullPageComponent
{
	public array $breadcrumb = [];

	public function render()
	{
		return $this->view()
			->layout('layouts::app', ['livewireFullPage' => true, 'breadcrumb' => $this->breadcrumb]);
	}
}
