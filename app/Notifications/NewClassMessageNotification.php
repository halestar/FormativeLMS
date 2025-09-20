<?php

namespace App\Notifications;

use App\Classes\NotificationPayload;
use App\Models\SubjectMatter\Components\ClassMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class NewClassMessageNotification extends Notification
{
	use Queueable;
	
	
	/**
	 * Create a new notification instance.
	 */
	public function __construct(public ClassMessage $message) {}
	
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
		$notification = new NotificationPayload(__('notifications.messages.title',
			['sender' => $this->message->postedBy->name]),
			$this->message->message);
		$notification->icon = '<i class="fa-solid fa-comments"></i>';
		$notification->bgColor = 'white';
		$notification->textColor = 'black';
		$notification->borderColor = 'lightblue';
		$notification->url = route('subjects.school.classes.messages', ['notification_id' => $this->id]);
		$notification->misc =
			[
				'message_id' => $this->message->id,
				'session_id' => $this->message->session_id,
				'student_id' => $this->message->student_id,
			];
		return $notification->toArray();
	}
	
	public function broadcastType(): string
	{
		return 'new-class-message';
	}
}
