<?php

return
	[
		'content' => 'Email Body',
		'password.reset' => 'Password Reset Message',
		'password.reset.body' => <<<EOE
<p>
You are receiving this email because we received a password reset request for your account.  Please enter the
following code in your browser to proceed with the reset:
</p>
<div>{!! \$token !!}</div>
<p>Thank you,</p>
EOE,
		'password.reset.description' => 'Message sent to users when they\'re trying to reset their password.',
		'password.reset.recipient' => 'Recipient\'s Name',
		'password.reset.recipient_email' => 'Recipient\'s Email',
		'password.reset.subject' => 'A request to reset your password has been received.',
		'password.reset.token' => 'Authentication Token',
		'login.link' => 'Login Link',
		'login.link.description' => 'Email sent as a login link to the user when they\'re trying to login.',
		'login.link.subject' => 'A link to login to your account has been requested.',
		'login.link.body' => <<<EOE
<p>
You are receiving this email because we received a login link request for your account.  Please click the link below to proceed:
<div>{!! \$url !!}</div>
<p>Thank you,</p>
EOE,
		'login.link.subject.short' => 'Link to to login',
		'login.link.body.short' => 'Use this link to login to FabLMS: {!! \$url !!}',
		'login.link.url' => 'Login Link URL',
		'preview' => 'Preview Email',
		'revert.confirm' => 'Are you sure you wish to undo your changes?',
		'send.test' => 'Send Test Email to Myself',
		'subject' => 'Subject',
		'test.sent.message' => 'A test message with this content was sent to :email',
		'test.sent.title' => 'Test Message Sent',
        'attachments' => 'E-Mail Attachments',
        'class.status.name' => 'Update to Class Status',
        'class.status.description' => 'Notification sent to subscribers of the class updates',
        'class.status.subject.short' => 'Class Status Update',
        'class.status.body.short' => '{!! $class_name !!} status has been updated to {!! $status !!}',
        'class.status.tokens.class_name' => 'Class Name',
        'class.status.tokens.status' => 'Status',
        'class.status.tokens.teachers' => 'Teacher Names',
        'class.status.tokens.poster_name' => 'Poster\s Name',
        'class.status.tokens.expiry' => 'Expiry Date',
        'class.messages.name' => 'Class Message',
        'class.messages.description' => 'Notification sent to subscribers of the class messages',
        'class.messages.subject.short' => 'New Class Message',
        'class.messages.body.short' => '{!! $message !!}',
        'class.messages.tokens.message' => 'Class Message',
        'class.messages.tokens.posted.by' => 'Posted By',
        'class.messages.tokens.posted.on' => 'Posted On',
        'class.activity.name' => 'Class Activity',
        'class.activity.description' => 'This notification is used for class activities, such as a class announcement posted, or a new link added. These are not for Learning Demonstrations.',
        'class.activity.tokens.activity_action' => 'Class Activity Action (e.g. New Announcement)',
        'class.activity.subject.short' => 'New Class Activity',
        'class.activity.body.short' => '{!! $activity_action !!} was posted in {!! $class_name !!}',
		'learning.demonstration.posted.name' => 'New Learning Demonstration Posted',
		'learning.demonstration.posted.description' => 'This notification is used whenever a new Learning Demonstration is posted.',
		'learning.demonstration.posted.subject.short' => 'New Learning Demonstration Opportunity Posted',
		'learning.demonstration.posted.body.short' => 'A new Learning Demonstration Opportunity was posted in {!! $class_name !!}',
		'learning.demonstration.updated.name' => 'Learning Demonstration Updated',
		'learning.demonstration.updated.description' => 'This notification is used whenever a Learning Demonstration is updated.',
		'learning.demonstration.updated.subject.short' => 'Learning Demonstration Opportunity Updated',
		'learning.demonstration.updated.body.short' => 'The Learning Demonstration Opportunity {!! $demonstration_name !!} was updated in {!! $class_name !!}',
		'learning.demonstration.deleted.name' => 'Learning Demonstration Deleted',
		'learning.demonstration.deleted.description' => 'This notification is used whenever a Learning Demonstration is deleted.',
		'learning.demonstration.deleted.subject.short' => 'Learning Demonstration Opportunity Deleted',
		'learning.demonstration.deleted.body.short' => 'The Learning Demonstration Opportunity {!! $demonstration_name !!} was deleted in {!! $class_name !!}',
		'substitutes.verification.new' => 'New Substitute Verification',
		'substitutes.verification.new.description' => 'Notification sent to a substitute in order to verify their account.',
		'substitutes.verification.new.subject' => 'New Sub Verification',
		'substitutes.verification.new.url' => 'Authentication Link',
		'substitutes.verification.new.body' => <<<EOE
Dear {!! \$recipient !!},
<p>
    If you're receiving this message, you have been signed up for the FabLMS substitute system.
    This system allows our teachers to request substitutes for a day they will be absent and transmit this
    request to all substitutes available and gives them a chance to accept the job.  This email is to verify
    that you have signed up for this program and to gather some communication information about yourself.
    If this was sent to you in error, please feel free to disregard this email. You can also contact
    <a href="mailto:fablms@kalinec.net">the administrator</a> to ask to be removed.
</p>
<p>
    If you're interested in becoming a substitute for New Roads School, please complete
   the form by clicking this link: {!! \$url !!}
</p>
<p>Thank you,</p>
<p>FabLMS</p>
EOE,
		'substitutes.verification.new.subject.short' => 'New Sub Verification',
		'substitutes.verification.new.body.short' => 'Click <a href="{!! $url !!}">here</a> to verify your account',

		'substitutes.welcome' => 'New Substitute Welcome Message',
		'substitutes.welcome.description' => 'Message sent to new substitutes once they\'ve been verified.',
		'substitutes.welcome.email_status' => 'Enabled/Disabled Email Notifications',
		'substitutes.welcome.sms_status' => 'Enabled with phone number or disabled',
		'substitutes.welcome.campuses' => 'List of campuses assigned',
		'substitutes.welcome.subject' => 'Welcome to New Roads Substitutes',
		'substitutes.welcome.body' => <<<EOE
Dear {!! \$recipient !!},
<p>Welcome to the FabLMS substitute pool. Thank you for completing your profile.</p>
<p>Your current notification settings are:</p>
<ul>
    <li>Email notifications: {!! \$email_status !!}</li>
    <li>
        Text notifications:
        {!! \$sms_status !!}
    </li>
    <li>
        Campuses:
        {!! \$campuses !!}
    </li>
</ul>
<p>You will begin receiving substitute request notifications immediately based on these settings.</p>
<p>Thank you,</p>
<p>FabLMS</p>
EOE,
		'substitutes.welcome.subject.short' => 'Welcome to New Roads Substitutes',
		'substitutes.welcome.body.short' => 'You have joined the FabLMS Sub Requests texting channel.  You will get notifications about new substitute requests from this number. To stop receiving these notifications, reply STOP to this number',


		'substitutes.new.signup' => 'New Substitute Sign Up Notification',
		'substitutes.new.signup.link_to_profile' => 'Link to Substitute Profile',
		'substitutes.new.signup.description' => 'Message sent to administration when a new substitutes completes the verification process.',
		'substitutes.new.signup.subject' => 'New Substitute Signup',
		'substitutes.new.signup.subject.short' => 'New Sub Signup',
		'substitutes.new.signup.body' => <<<EOE
A new substitute, {!! \$recipient !!} has been accepted as a Substitute for {!! \$campuses !!}
EOE,
		'substitutes.new.signup.body.short' => 'A new substitute, {!! \$recipient !!} has been accepted',

		'substitutes.new.request' => 'New Substitute Request',
		'substitutes.new.request.description' => 'Message sent to all available substitutes when there is a sub request entered.',
		'substitutes.new.request.subject.short' => 'New Substitute Request',
		'substitutes.new.request.subject' => 'New Substitute Request',
		'substitutes.new.request.body.short' => <<<EOE
Coverage is needed for {!! \$teacher_name !!} on {!! \$coverage_date !!} from {!! \$coverage_start !!} to {!! \$coverage_end !!}'.
Please go to {!! \$link !!} to accept.
EOE,
		'substitutes.new.request.body' => <<<EOE
<h2>New Substitute Opportunity</h2>
<p>Coverage is needed for {!! \$teacher_name !!} on {!! \$coverage_date !!} from {!! \$coverage_start !!} to {!! \$coverage_end !!}.</p>
<p>Use this link to review and accept the coverage: {!! \$link !!}</p>
<p>If the request has already been accepted, the link will show that it is no longer available.</p>
EOE,
		'substitutes.new.request.teacher' => 'Name of the teacher submitting the request',
		'substitutes.new.request.date' => 'Date requested for coverage',
		'substitutes.new.request.start' => 'Start time requested for coverage',
		'substitutes.new.request.end' => 'End time requested for coverage',
		'substitutes.new.request.link' => 'Link to accept the coverage request',

		'substitutes.new.request.admin' => 'New Substitute Request Administrator Notification',
		'substitutes.new.request.admin.description' => 'Message sent to administration advising them that a new substitute request was entered',
		'substitutes.new.request.admin.subject.short' => 'New Substitute Request',
		'substitutes.new.request.admin.subject' => 'New Substitute Request',
		'substitutes.new.request.admin.body.short' => <<<EOE
A new substitute request has been submitted by {!! \$teacher_name !!} for {!! \$coverage_date !!}.
EOE,
		'substitutes.new.request.admin.body' => <<<EOE
<h2>New Substitute Request Submitted</h2>

<p>
	{!! \$teacher_name !!} requested substitute coverage for {!! \$teacher_name !!} on {!! \$coverage_date !!} 
	from {!! \$coverage_start !!} to {!! \$coverage_end !!}.
</p>
<p>The following substitutes were contacted about this request: {!! \$subs_contacted !!}</p>
<p>Use this link to view the request: {!! \$link !!}</p>
EOE,
		'substitutes.new.request.admin.subs_contacted' => 'List of all the substitutes contacted about this request',
		'substitutes.new.request.admin.link' => 'Link to view the request',

		'substitutes.new.request.teachers' => 'Coverage Request Teacher Notification',
		'substitutes.new.request.teachers.description' => 'Message sent to the teacher who entered a sub request acknowledging that their request was submitted',
		'substitutes.new.request.teachers.subject.short' => 'Coverage Request Submitted',
		'substitutes.new.request.teachers.subject' => 'Coverage Request Submitted',
		'substitutes.new.request.teachers.body.short' => 'Your request for coverage has been submitted.',
		'substitutes.new.request.teachers.body' => <<<EOE
<h2>Coverage Request Submitted</h2>
<p>Request for coverage on {!! \$coverage_date !!} has been received.</p>
EOE,

		'substitutes.accepted.request.admin' => 'Coverage Accepted Admin Notification',
		'substitutes.accepted.request.admin.description' => 'Message sent to the the administration that a substitute request has been accepted',
		'substitutes.accepted.request.admin.subject.short' => 'Substitute Accepted Coverage',
		'substitutes.accepted.request.admin.subject' => 'Substitute Accepted Coverage',
		'substitutes.accepted.request.admin.body.short' => 'A substitute request has been accepted by {!! $substitute_name !!} for {!! \$coverage_date !!}.',
		'substitutes.accepted.request.admin.body' => <<<EOE
<h2>Substitute Accepted Coverage</h2>
<p>
    {!! \$substitute_name !!} ({!! \$substitute_email !!}, {!! \$substitute_phone !!}) accepted coverage for 
    {!! \$teacher_name !!} on {!! \$coverage_date !!} 
    from {!! \$coverage_start !!} to {!! \$coverage_end !!}.
</p>
<p>Use this link to see the request: {!! \$link !!}</p>
EOE,
		'substitutes.accepted.request.admin.substitute.name' => 'Name of the substitute accepting the request',
		'substitutes.accepted.request.admin.substitute.email' => 'Email of the substitute accepting the request',
		'substitutes.accepted.request.admin.substitute.phone' => 'Phone number of the substitute accepting the request, if one is provided',

		'substitutes.accepted.request.sub' => 'Coverage Accepted Substitute Notification',
		'substitutes.accepted.request.sub.description' => 'Message sent to the substitute acknowledging that their request has been accepted.',
		'substitutes.accepted.request.sub.subject.short' => 'Coverage Confirmation',
		'substitutes.accepted.request.sub.subject' => 'Coverage Confirmation',
		'substitutes.accepted.request.sub.body.short' => 'You are now scheduled to cover {!! $teacher_name !!} on {!! $coverage_date !!} from {!! $coverage_start !!} to {!! $coverage_end !!}.',
		'substitutes.accepted.request.sub.body' => <<<EOE
<h2>Coverage Confirmed</h2>
<p>
    You are now scheduled to cover {!! \$teacher_name !!} on {!! \$coverage_date !!} 
    from {!! \$coverage_start !!} to {!! \$coverage_end !!}.
</p>
<p>Classes You Are Covering</p>
{!! \$classes_table !!}
<p>Thank you for covering this request.</p>
EOE,
		'substitutes.accepted.request.sub.classes_table' => 'Table of classes the substitute is covering',
		'substitutes.accepted.request.sub.classes_table.no' => 'No class details were found for this assignment.',

		'substitutes.accepted.request.teacher' => 'Coverage Accepted Teacher Notification',
		'substitutes.accepted.request.teacher.description' => 'Message sent to the teacher advising them that coverage for their request has been found.',
		'substitutes.accepted.request.teacher.subject.short' => 'Substitute Accepted Coverage',
		'substitutes.accepted.request.teacher.subject' => 'Substitute Accepted Coverage',
		'substitutes.accepted.request.teacher.body.short' => 'You are now scheduled to cover {!! $teacher_name !!} on {!! $coverage_date !!} from {!! $coverage_start !!} to {!! $coverage_end !!}.',
		'substitutes.accepted.request.teacher.body' => <<<EOE
<h2>Substitute Coverage Confirmed</h2>
<p>Coverage has been found for all (or some) of your classes.</p>
<p>
    {!! \$substitute_name !!} will provide coverage on {!! \$coverage_date !!} 
    from {!! \$coverage_start !!} to {!! \$coverage_end !!}.
</p>
<p>Classes They Are Covering</p>
{!! \$classes_table !!}
EOE,

		'substitutes.rejected.request' => 'Substitute Rejection Notification',
		'substitutes.rejected.request.description' => 'Message sent substitutes to the substitutes who did not accept the coverage request.',
		'substitutes.rejected.request.subject.short' => 'Coverage has been found',
		'substitutes.rejected.request.subject' => 'Coverage has been found',
		'substitutes.rejected.request.body.short' => 'Coverage has been found for {!! $coverage_date !!}. Thank you for participating.',
		'substitutes.rejected.request.body' => <<<EOE
<h2>Coverage Has Been Filled</h2>
<p style="margin:0 0 16px 0;color:#212529;">
    Thank you for your quick response and continued support.
    Coverage has now been finalized for {!! \$teacher_name !!} on {!! \$coverage_date !!} 
    from {!! \$coverage_start !!} to {!! \$coverage_end !!}.
</p>
<p>
    No action is needed on your part for this request. We appreciate your availability and will continue
    to notify you when future opportunities are open.
</p>
EOE,
	];