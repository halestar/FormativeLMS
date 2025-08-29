<?php

namespace App\Livewire\Utilities;

use App\Classes\Settings\EmailSetting;
use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\Work\WorkStorage;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
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
	public string $reloadKey = '';
	
	protected function rules()
	{
		return [
			'subject' => 'required|min:10|max:255',
			'content' =>
				[
					'required',
					function(string $attribute, mixed $value, Closure $fail) {
						//the text of the email is located in the $value var, so we need to make sure that it contains
						//the require token from the class.
						$requiredTokens = ($this->emailClass)::requiredTokens();
						$tokenNames = ($this->emailClass)::availableTokens();
						foreach($requiredTokens as $token) {
							if($value == "" || !str_contains($value, $token)) {
								Log::debug("Missing token: $token");
								$fail('errors.emails.tokens.missing')
									->translate(['token' => $tokenNames[$token] ?? "Token"]);
							}
						}
					}
				],
		];
	}
	
	#[Computed]
	public function isDirty(): bool
	{
		return $this->emailSetting->subject != $this->subject || $this->emailSetting->content != $this->content;
	}
	
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
			$this->emailSetting->cleanup();
			$this->subject = $this->emailSetting->subject;
			$this->content = $this->emailSetting->content;
			$this->editing = true;
			$this->reloadKey = uniqid();
		}
		else {
			$this->subject = "";
			$this->content = "";
			$this->editing = false;
		}
	}
	
	public function updateEmail()
	{
		$this->validate();
		$this->emailSetting->subject = $this->subject;
		$this->emailSetting->content = $this->content;
		$this->emailSetting->save();
	}
	
	public function close()
	{
		$this->subject = "";
		$this->content = "";
		$this->editing = false;
		$this->emailSetting->cleanup();
		$this->emailSetting = null;
	}
	
	public function revert()
	{
		$this->emailSetting->cleanup();
		$this->subject = $this->emailSetting->subject;
		$this->content = $this->emailSetting->content;
		$this->reloadKey = uniqid();
	}
	
	
	public function send()
	{
		Mail::to(Auth::user()->system_email)
		    ->send(($this->emailClass)::testEmail(Auth::user()));
		$msg = __('emails.test.sent.title');
		$message = __('emails.test.sent.message', ['email' => Auth::user()->system_email]);
		$this->js('new LmsToast("' . $msg . '", "' . $message . '")');
	}
	
	public function render()
	{
		return view('livewire.utilities.school-emails-editor')
			->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
			->section('content');
	}
}
