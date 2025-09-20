<?php

namespace App\Notifications;

use App\Classes\NotificationPayload;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ClassAlert extends Notification implements ShouldQueue
{
	use Queueable;
	
	
	/**
	 * Create a new notification instance.
	 */
	public function __construct(public ClassSession $classSession, public NotificationPayload $payload)
	{
		$this->payload->bgColor = 'lightblue';
		$this->payload->textColor = 'black';
		$this->payload->url = route('subjects.school.classes.show', ['classSession' => $this->classSession->id]);
		$this->payload->borderColor = "lightblue";
		$this->payload->icon = '<i class="fa-solid fa-chalkboard-user"></i>';
	}
	
	/**
	 * Get the notification's delivery channels.
	 *
	 * @return array<int, string>
	 */
	public function via(object $notifiable): array
	{
		return ['database', 'broadcast'];
	}
	
	public function toBroadcast(object $notifiable): BroadcastMessage
	{
		return new BroadcastMessage($this->toArray($notifiable));
	}
	
	/**
	 * Get the array representation of the notification.
	 *
	 * @return array<string, mixed>
	 */
	public function toArray(object $notifiable): array
	{
		$arr = $this->payload->toArray();
		$arr['session_id'] = $this->classSession->id;
		return $arr;
	}
	
	public function broadcastType(): string
	{
		return 'class-alert';
	}
}
