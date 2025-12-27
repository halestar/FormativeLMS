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
	];