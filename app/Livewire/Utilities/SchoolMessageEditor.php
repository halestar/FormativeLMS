<?php

namespace App\Livewire\Utilities;

use App\Channels\SchoolMailer;
use App\Channels\SchoolTexter;
use App\Classes\Settings\CommunicationSettings;
use App\Models\Utilities\SchoolMessage;
use Closure;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SchoolMessageEditor extends Component
{
	public array $breadcrumb;
	public SchoolMessage $message;
    public bool $forceSubscribe;
    public string $name;
    public ?string $description;
    public bool $sendEmail;
    public bool $sendSms;
    public bool $sendPush;
    public ?string $subject = null;
    public ?string $body = null;
    public ?string $shortSubject = null;
    public ?string $shortBody = null;
    public bool $enabled;
    public bool $canSms;
	
	#[Computed]
	public function isDirty(): bool
	{
		return $this->message->subject != $this->subject || $this->message->body != $this->body ||
            $this->message->short_subject != $this->shortSubject || $this->message->short_body != strip_tags($this->shortBody) ||
            $this->message->force_subscribe != $this->forceSubscribe || $this->message->name != $this->name ||
            $this->message->description != $this->description || $this->message->send_email != $this->sendEmail ||
            $this->message->send_sms != $this->sendSms || $this->message->send_push != $this->sendPush ||
            $this->enabled != $this->message->enabled;
	}

	
	public function mount(SchoolMessage $message, CommunicationSettings $commSettings)
	{
		$this->breadcrumb =
            [
                __('system.menu.school.settings') => route('settings.school'),
                __('school.messages') => "#"
            ];
		$this->message = $message;
        $this->canSms = $commSettings->send_sms;
        $this->revert();
	}
	
	public function updateMessage()
	{
		$this->validate();
		$this->message->force_subscribe = $this->forceSubscribe;
		$this->message->name = $this->name;
        $this->message->description = $this->description;
        $this->message->send_email = $this->sendEmail;
        $this->message->send_sms = $this->sendSms;
        $this->message->send_push = $this->sendPush;
        $this->message->subject = $this->subject;
        $this->message->body = $this->body;
        $this->message->short_subject = $this->shortSubject;
        $this->message->short_body = strip_tags($this->shortBody);
        $this->message->enabled = $this->enabled;
		$this->message->save();
	}

	public function revert()
	{
		$this->message->cleanup();
		$this->forceSubscribe = $this->message->force_subscribe;
        $this->name = $this->message->name;
        $this->description = $this->message->description;
        $this->sendEmail = $this->message->send_email;
        $this->sendSms = $this->message->send_sms;
        $this->sendPush = $this->message->send_push;
        $this->subject = $this->message->subject;
        $this->body = $this->message->body?? '';
        $this->shortSubject = $this->message->short_subject;
        $this->shortBody = $this->message->short_body?? '';
        $this->enabled = $this->message->enabled;
	}
	
	public function testEmail()
	{
		$notification = ($this->message->notification_class)::fakeNotification($this->message)->onlyThrough([SchoolMailer::class]);
        Auth::user()->notify($notification);
        $this->dispatch('school-message-editor.message-sent', el: '#send-test-email-btn');
	}

    public function testSms()
    {
        $notification = ($this->message->notification_class)::fakeNotification($this->message)->onlyThrough([SchoolTexter::class]);
        Auth::user()->notify($notification);
        $this->dispatch('school-message-editor.message-sent', el: '#send-test-sms-btn');
    }

    public function testPush()
    {
        $notification = ($this->message->notification_class)::fakeNotification($this->message)->onlyThrough(['broadcast','database']);
        Auth::user()->notify($notification);
        $this->dispatch('school-message-editor.message-sent', el: '#send-test-push-btn');
    }
	
	public function render()
	{
		return view('livewire.utilities.school-message-editor')
			->extends('layouts.app', ['breadcrumb' => $this->breadcrumb])
			->section('content');
	}
	
	protected function rules()
	{
		return [
            'forceSubscribe' => 'required|boolean',
            'name' => 'required|min:3|max:255',
            'description' => 'nullable|min:10|max:255',
            'sendEmail' => 'required|boolean',
            'sendSms' => 'required|boolean',
            'sendPush' => 'required|boolean',
            'subject' => 'nullable|min:10|max:255',
            'shortSubject' => 'nullable|min:10|max:255',
			'body' =>
				[
					'nullable',
					function(string $attribute, mixed $value, Closure $fail)
					{
						//the text of the email is located in the $value var, so we need to make sure that it contains
						//the require token from the class.
						$requiredTokens = ($this->message->notification_class)::requiredTokens();
						$tokenNames = ($this->message->notification_class)::availableTokens();
						foreach($requiredTokens as $token)
						{
							if($value == "" || !str_contains($value, $token))
							{
								$fail('errors.emails.tokens.missing')
									->translate(['token' => $tokenNames[$token] ?? "Token"]);
							}
						}
					}
				],
            'shortBody' =>
                [
                    'nullable',
                    function(string $attribute, mixed $value, Closure $fail)
                    {
                        //the text of the email is located in the $value var, so we need to make sure that it contains
                        //the require token from the class.
                        $requiredTokens = ($this->message->notification_class)::requiredTokens();
                        $tokenNames = ($this->message->notification_class)::availableTokens();
                        foreach($requiredTokens as $token)
                        {
                            if($value == "" || !str_contains($value, $token))
                            {
                                $fail('errors.emails.tokens.missing')
                                    ->translate(['token' => $tokenNames[$token] ?? "Token"]);
                            }
                        }
                    }
                ],
		];
	}
}
