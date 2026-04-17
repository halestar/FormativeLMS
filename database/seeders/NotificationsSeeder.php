<?php

namespace Database\Seeders;

use App\Models\Utilities\SchoolMessage;
use App\Models\Utilities\SchoolRoles;
use App\Notifications\Substitutes\AcceptedRequestAdminNotification;
use App\Notifications\Substitutes\AcceptedRequestSubstituteNotification;
use App\Notifications\Substitutes\AcceptedRequestTeacherNotification;
use App\Notifications\Substitutes\NewRequestAdminNotification;
use App\Notifications\Substitutes\NewRequestSubNotification;
use App\Notifications\Substitutes\NewSubRequestTeacherNotification;
use App\Notifications\Substitutes\NewSubSignupNotification;
use App\Notifications\Substitutes\NewSubstituteVerification;
use App\Notifications\Auth\LoginLinkNotification;
use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Classes\ClassAlert;
use App\Notifications\Classes\NewClassMessageNotification;
use App\Notifications\Classes\NewClassStatusNotification;
use App\Notifications\Learning\LearningDemonstrationDeletedNotification;
use App\Notifications\Learning\LearningDemonstrationPostedNotification;
use App\Notifications\Learning\LearningDemonstrationUpdatedNotification;
use App\Notifications\Substitutes\NewSubstituteWelcomeNotification;
use App\Notifications\Substitutes\RejectSubsNotification;
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
            //New Substitute Verification
            [
	            'system' => true,
	            'subscribable' => false,
	            'force_subscribe' => true,
	            'name' => __('emails.substitutes.verification.new'),
	            'description' => __('emails.substitutes.verification.new.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.verification.new.subject'),
	            'body' => __('emails.substitutes.verification.new.body'),
	            'short_subject' => __('emails.substitutes.verification.new.subject.short'),
	            'short_body' => __('emails.substitutes.verification.new.body.short'),
	            'notification_class' => NewSubstituteVerification::class,
	            'default_roles' => [],
            ],
            //New Substitute Welcome Notification
            [
	            'system' => true,
	            'subscribable' => false,
	            'force_subscribe' => true,
	            'name' => __('emails.substitutes.welcome'),
	            'description' => __('emails.substitutes.welcome.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.welcome.subject'),
	            'body' => __('emails.substitutes.welcome.body'),
	            'short_subject' => __('emails.substitutes.welcome.subject.short'),
	            'short_body' => __('emails.substitutes.welcome.body.short'),
	            'notification_class' => NewSubstituteWelcomeNotification::class,
	            'default_roles' => [SchoolRoles::$SUBSTITUTE],
            ],
            //New Sub Signup Notification
            [
	            'system' => true,
	            'subscribable' => true,
	            'force_subscribe' => false,
	            'name' => __('emails.substitutes.new.signup'),
	            'description' => __('emails.substitutes.new.signup.description'),
	            'send_email' => true,
	            'send_sms' => false,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.new.signup.subject'),
	            'body' => __('emails.substitutes.new.signup.body'),
	            'short_subject' => __('emails.substitutes.new.signup.subject.short'),
	            'short_body' => __('emails.substitutes.new.signup.body.short'),
	            'notification_class' => NewSubSignupNotification::class,
	            'default_roles' => [SchoolRoles::$STAFF],
            ],
            //New Request Sub Notification
            [
	            'system' => true,
	            'subscribable' => false,
	            'force_subscribe' => true,
	            'name' => __('emails.substitutes.new.request'),
	            'description' => __('emails.substitutes.new.request.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.new.request.subject'),
	            'body' => __('emails.substitutes.new.request.body'),
	            'short_subject' => __('emails.substitutes.new.request.subject.short'),
	            'short_body' => __('emails.substitutes.new.request.body.short'),
	            'notification_class' => NewRequestSubNotification::class,
	            'default_roles' => [],
            ],
            //New Request Admin Notification
            [
	            'system' => true,
	            'subscribable' => true,
	            'force_subscribe' => false,
	            'name' => __('emails.substitutes.new.request.admin'),
	            'description' => __('emails.substitutes.new.request.admin.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.new.request.admin.subject'),
	            'body' => __('emails.substitutes.new.request.admin.body'),
	            'short_subject' => __('emails.substitutes.new.request.admin.subject.short'),
	            'short_body' => __('emails.substitutes.new.request.admin.body.short'),
	            'notification_class' => NewRequestAdminNotification::class,
	            'default_roles' => [SchoolRoles::$STAFF],
            ],
            //New Sub Request Teacher Notification
            [
	            'system' => true,
	            'subscribable' => false,
	            'force_subscribe' => true,
	            'name' => __('emails.substitutes.new.request.teachers'),
	            'description' => __('emails.substitutes.new.request.teachers.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.new.request.teachers.subject'),
	            'body' => __('emails.substitutes.new.request.teachers.body'),
	            'short_subject' => __('emails.substitutes.new.request.teachers.subject.short'),
	            'short_body' => __('emails.substitutes.new.request.teachers.body.short'),
	            'notification_class' => NewSubRequestTeacherNotification::class,
	            'default_roles' => [],
            ],
            //Accepted Request Admin Notification
            [
	            'system' => true,
	            'subscribable' => true,
	            'force_subscribe' => false,
	            'name' => __('emails.substitutes.accepted.request.admin'),
	            'description' => __('emails.substitutes.accepted.request.admin.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.accepted.request.admin.subject'),
	            'body' => __('emails.substitutes.accepted.request.admin.body'),
	            'short_subject' => __('emails.substitutes.accepted.request.admin.subject.short'),
	            'short_body' => __('emails.substitutes.accepted.request.admin.body.short'),
	            'notification_class' => AcceptedRequestAdminNotification::class,
	            'default_roles' => [SchoolRoles::$STAFF],
            ],
            //Accepted Request Substitute Notification
            [
	            'system' => true,
	            'subscribable' => false,
	            'force_subscribe' => true,
	            'name' => __('emails.substitutes.accepted.request.sub'),
	            'description' => __('emails.substitutes.accepted.request.sub.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.accepted.request.sub.subject'),
	            'body' => __('emails.substitutes.accepted.request.sub.body'),
	            'short_subject' => __('emails.substitutes.accepted.request.sub.subject.short'),
	            'short_body' => __('emails.substitutes.accepted.request.sub.body.short'),
	            'notification_class' => AcceptedRequestSubstituteNotification::class,
	            'default_roles' => [],
            ],
            //Accepted Request Teacher Notification
            [
	            'system' => true,
	            'subscribable' => false,
	            'force_subscribe' => true,
	            'name' => __('emails.substitutes.accepted.request.teacher'),
	            'description' => __('emails.substitutes.accepted.request.teacher.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.accepted.request.teacher.subject'),
	            'body' => __('emails.substitutes.accepted.request.teacher.body'),
	            'short_subject' => __('emails.substitutes.accepted.request.teacher.subject.short'),
	            'short_body' => __('emails.substitutes.accepted.request.teacher.body.short'),
	            'notification_class' => AcceptedRequestTeacherNotification::class,
	            'default_roles' => [],
            ],

            //Reject Subs Notification
            [
	            'system' => true,
	            'subscribable' => false,
	            'force_subscribe' => true,
	            'name' => __('emails.substitutes.rejected.request'),
	            'description' => __('emails.substitutes.rejected.request.description'),
	            'send_email' => true,
	            'send_sms' => true,
	            'send_push' => false,
	            'subject' => __('emails.substitutes.rejected.request.subject'),
	            'body' => __('emails.substitutes.rejected.request.body'),
	            'short_subject' => __('emails.substitutes.rejected.request.subject.short'),
	            'short_body' => __('emails.substitutes.rejected.request.body.short'),
	            'notification_class' => RejectSubsNotification::class,
	            'default_roles' => [],
            ],
        ];
    }
}
