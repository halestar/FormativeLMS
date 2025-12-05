<?php

namespace App\Notifications;

use App\Channels\SchoolMailer;
use App\Channels\SchoolTexter;
use App\Classes\Settings\CommunicationSettings;
use App\Mail\SchoolMail;
use App\Models\Utilities\SchoolMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Blade;

abstract class LmsNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public ?array $onlyThrough = null;
    public SchoolMessage $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(SchoolMessage $message = null)
    {
		if(!$message)
            $this->message = SchoolMessage::where('notification_class', static::class)
	            ->where('system', true)
	            ->first();
		else
			$this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    final public function via(object $notifiable): array
    {
        if($this->onlyThrough) return $this->onlyThrough;
        $commSettings = app(CommunicationSettings::class);
        $channels = [];
        if(!$this->message->subscribable || $this->message->force_subscribe || $this->message->isSubscribed($notifiable))
        {
            //we're sending the message, so figure out the channels.
            if($this->message->send_email)
                $channels[] = SchoolMailer::class;
            if($this->message->send_sms && $commSettings->send_sms)
                $channels[] = SchoolTexter::class;
            if($this->message->send_push)
            {
                $channels[] = 'database';
                $channels[] = 'broadcast';
            }
        }
        return $channels;
    }

    public function toMail(): SchoolMail
    {
        return new SchoolMail($this);
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $data = $this->toArray($notifiable);
        return new BroadcastMessage($data);
    }

    public function toArray(object $notifiable): array
    {
        $data = $this->message->toRenderedArray($this->withTokens());
        $data['action_link'] = $this->actionLink();
        return $data;
    }

    public function onlyThrough(array $channels): static
    {
        $this->onlyThrough = $channels;
        return $this;
    }

	public function broadcastAs(): string
	{
		return 'lmsNotification';
	}

    public function databaseType(object $notifiable): string
    {
        return 'lms-notification';
    }

    abstract public static function availableTokens(): array;
    abstract public function withTokens(): array;
    abstract public static function requiredTokens(): array;
    abstract public static function fakeNotification(): static;
    abstract public function actionLink(): string|null;


}
