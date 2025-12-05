<?php

namespace Database\Seeders;

use App\Models\Utilities\SchoolMessage;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Classes\ClassAlert;
use App\Notifications\Classes\NewClassMessageNotification;
use App\Notifications\Classes\NewClassStatusNotification;
use App\Notifications\Learning\LearningDemonstrationPostedNotification;
use App\Notifications\Learning\LearningDemonstrationUpdatedNotification;
use Illuminate\Database\Seeder;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Reset Password Notification
        SchoolMessage::insert($this->notifications());
    }

    private function notifications(): array
    {
        return
        [
            //Reset Password Notification
            [
                'system' => true,
                'subscribable' => false,
                'force_subscribe' => true,
                'name' => __('emails.password.reset'),
                'description' => __('emails.password.reset.description'),
                'send_email' => true,
                'send_sms' => false,
                'send_push' => false,
                'subject' => __('emails.password.reset.subject'),
                'body' => __('emails.password.reset.body'),
                'short_subject' => null,
                'short_body' => null,
                'notification_class' => ResetPasswordNotification::class,
            ],
            //Class Status Update Notification
            [
                'system' => true,
                'subscribable' => true,
                'force_subscribe' => false,
                'name' => __('emails.class.status.name'),
                'description' => __('emails.class.status.description'),
                'send_email' => false,
                'send_sms' => false,
                'send_push' => true,
                'subject' => null,
                'body' => null,
                'short_subject' => __('emails.class.status.subject.short'),
                'short_body' => __('emails.class.status.body.short'),
                'notification_class' => NewClassStatusNotification::class,
            ],
            //New Class Message Notification
            [
                'system' => true,
                'subscribable' => true,
                'force_subscribe' => false,
                'name' => __('emails.class.messages.name'),
                'description' => __('emails.class.messages.description'),
                'send_email' => false,
                'send_sms' => false,
                'send_push' => true,
                'subject' => null,
                'body' => null,
                'short_subject' => __('emails.class.messages.subject.short'),
                'short_body' => __('emails.class.messages.body.short'),
                'notification_class' => NewClassMessageNotification::class,
            ],
            //New Class Alert Notification
            [
                'system' => true,
                'subscribable' => true,
                'force_subscribe' => false,
                'name' => __('emails.class.activity.name'),
                'description' => __('emails.class.activity.description'),
                'send_email' => false,
                'send_sms' => false,
                'send_push' => true,
                'subject' => null,
                'body' => null,
                'short_subject' => __('emails.class.activity.subject.short'),
                'short_body' => __('emails.class.activity.body.short'),
                'notification_class' => ClassAlert::class,
            ],
	        //New Learning Demonstration Posted Notification
	        [
		        'system' => true,
		        'subscribable' => true,
		        'force_subscribe' => false,
		        'name' => __('emails.learning.demonstration.posted.name'),
		        'description' => __('emails.learning.demonstration.posted.description'),
		        'send_email' => false,
		        'send_sms' => false,
		        'send_push' => true,
		        'subject' => null,
		        'body' => null,
		        'short_subject' => __('emails.learning.demonstration.posted.subject.short'),
		        'short_body' => __('emails.learning.demonstration.posted.body.short'),
		        'notification_class' => LearningDemonstrationPostedNotification::class,
	        ],
	        //New Learning Demonstration Updated Notification
	        [
		        'system' => true,
		        'subscribable' => true,
		        'force_subscribe' => false,
		        'name' => __('emails.learning.demonstration.updated.name'),
		        'description' => __('emails.learning.demonstration.updated.description'),
		        'send_email' => false,
		        'send_sms' => false,
		        'send_push' => true,
		        'subject' => null,
		        'body' => null,
		        'short_subject' => __('emails.learning.demonstration.updated.subject.short'),
		        'short_body' => __('emails.learning.demonstration.updated.body.short'),
		        'notification_class' => LearningDemonstrationUpdatedNotification::class,
	        ],
        ];
    }
}
