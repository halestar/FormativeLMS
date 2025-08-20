<?php

namespace App\Livewire\Utilities;

use App\Classes\Settings\EmailSetting;
use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\Work\WorkStorage;
use Livewire\Component;

class SchoolEmailsEditor extends Component
{
	public array $breadcrumb;
	public bool $editing = false;
	public ?EmailSetting $emailSetting = null;
	public string $subject = "";
	public string $content = "";
	public WorkStorage $workStorage;
	public string $emailClass;
	
	public function mount()
	{
		$this->breadcrumb = [__('system.menu.school.emails') => "#"];
		$storageSettings = app()->make(StorageSettings::class);;
		$this->workStorage = $storageSettings->email_work;
	}
	
	public function loadEmail(string $emailClass)
	{
		$this->emailClass = $emailClass;
		$this->emailSetting = ($emailClass)::getSetting();
		if($this->emailSetting) {
			$this->subject = $this->emailSetting->subject;
			$this->content = $this->emailSetting->content;
			$this->editing = true;
		}
		else {
			$this->subject = "";
			$this->content = "";
			$this->editing = false;
		}
	}
	
	public function render()
	{
		return view('livewire.utilities.school-emails-editor')
			->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
			->section('content');
	}
}
