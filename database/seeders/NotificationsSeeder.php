<?php

namespace Database\Seeders;

use App\Models\Utilities\SchoolMessage;
use App\Models\Utilities\SchoolRoles;
use App\Notifications\Auth\LoginLinkNotification;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Classes\ClassAlert;
use App\Notifications\Classes\NewClassMessageNotification;
use App\Notifications\Classes\NewClassStatusNotification;
use App\Notifications\Learning\LearningDemonstrationDeletedNotification;
use App\Notifications\Learning\LearningDemonstrationPostedNotification;
use App\Notifications\Learning\LearningDemonstrationUpdatedNotification;
use App\Notifications\Substitutes\AcceptedRequestAdminNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
		Schema::disableForeignKeyConstraints();
	    DB::table('default_roles')
		    ->where('model_type', SchoolMessage::class)
		    ->delete();
		SchoolMessage::truncate();
		Schema::enableForeignKeyConstraints();
        //Reset Password Notification
	    foreach($this->notifications() as $notification)
	    {
		    $msg = SchoolMessage::create(Arr::except($notification, 'default_roles'));
			if(count($notification['default_roles']) > 0)
			{
				$msg->defaultRoles()->sync(array_map(fn (
					$roleName) => SchoolRoles::findByName($roleName)->id, $notification['default_roles']));
				$msg->resetRoles();
			}
	    }
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
                'default_roles' => [],
            ],
            //Login Link Notification
            [
	            'system' => true,
	            'subscribable' => false,
	            'force_subscribe' => true,
	            'name' => __('emails.login.link'),
	            'description' => __('emails.login.link.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.login.link.subject'),
	            'body' => __('emails.login.link.body'),
	            'short_subject' => __('emails.login.link.subject.short'),
	            'short_body' => __('emails.login.link.body.short'),
	            'notification_class' => LoginLinkNotification::class,
	            'default_roles' => [],
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
                'default_roles' => [SchoolRoles::$FACULTY, SchoolRoles::$STUDENT, SchoolRoles::$PARENT],
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
                'default_roles' => [SchoolRoles::$FACULTY, SchoolRoles::$STUDENT, SchoolRoles::$PARENT],
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
                'default_roles' => [SchoolRoles::$FACULTY, SchoolRoles::$STUDENT, SchoolRoles::$PARENT],
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
		        'default_roles' => [SchoolRoles::$FACULTY, SchoolRoles::$STUDENT, SchoolRoles::$PARENT],
	        ],
	        //Learning Demonstration Updated Notification
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
		        'default_roles' => [SchoolRoles::$FACULTY, SchoolRoles::$STUDENT, SchoolRoles::$PARENT],
	        ],
	        //New Learning Demonstration Deleted Notification
	        [
		        'system' => true,
		        'subscribable' => true,
		        'force_subscribe' => false,
		        'name' => __('emails.learning.demonstration.deleted.name'),
		        'description' => __('emails.learning.demonstration.deleted.description'),
		        'send_email' => false,
		        'send_sms' => false,
		        'send_push' => true,
		        'subject' => null,
		        'body' => null,
		        'short_subject' => __('emails.learning.demonstration.deleted.subject.short'),
		        'short_body' => __('emails.learning.demonstration.deleted.body.short'),
		        'notification_class' => LearningDemonstrationDeletedNotification::class,
		        'default_roles' => [SchoolRoles::$FACULTY, SchoolRoles::$STUDENT, SchoolRoles::$PARENT],
	        ],
        ];
    }
}
