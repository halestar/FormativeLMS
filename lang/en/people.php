<?php

return
[

    'record.created' => 'Person Record was successfully created',
    'record.updated' => 'Person Record was successfully updated',
    'record.deleted' => 'Person Record was successfully deleted',

    'profile' => 'Profile',
    'profile.mine' => 'My Profile',
    'profile.view' => 'View Person Profile',
    'profile.image' => 'Profile Image',
    'profile.thumb' => 'Profile Thumbnail',
    'profile.school_id' => 'School ID',
    'profile.image.update' => 'Update Profile Image',
    'profile.image.remove' => 'Remove Profile Image',
    'profile.image.remove.confirm' => 'Are you sure you want to remove this profile image?',
    'profile.edit' => 'Edit Profile',
    'profile.editing' => 'Editing Profile',
    'profile.basic' => 'Basic Information',
    'profile.basic.update' => 'Update Basic Information',
    'profile.fields.update' => 'Update Fields Information',
    'profile.contact' => 'Contact Information',
    'profile.schedule.student' => 'Student Schedule',
    'profile.schedule.teacher' => 'Teaching Schedule',

    'profile.fields.first' => 'First Name',
    'profile.fields.last' => 'Last Name',
    'profile.fields.last.error' => 'A last name is required. Mac 255 characters, minimum of 1',
    'profile.fields.middle' => 'Middle Name',
    'profile.fields.nick' => 'Nick Name',
    'profile.fields.email' => 'Email',
    'profile.fields.preferred_name' => 'Preferred Name',
    'profile.fields.dob' => 'Date of Birth',
    'profile.fields.portrait' => 'User Portrait',
    'profile.fields.addresses' => 'Addresses',
    'profile.fields.phones' => 'Phones',
    'profile.fields.relationships' => 'Relationships',

    'profile.links.groups.settings' => 'Personal Settings',
    'profile.links.settings.personal_view_policy' => 'Privacy Settings',


    'policies.employee' => 'Employee Policy',
    'policies.student' => 'Student Policy',
    'policies.parent' => 'Parent Policy',

    'policies.view.viewable_policy' => 'Viewable Policy',
    'policies.view.viewable_policy.new' => 'New Viewable Policy',
    'policies.view.viewable_policy.update' => 'Update Viewable Policy',
    'policies.view.viewable_policy.name' => 'Viewable Policy Name',
    'policies.view.viewable_policy.name.error' => 'Please enter a policy name. Max 255 characters',
    'policies.view.viewable_policy.base_role' => 'Viewable Policy Base Role',
    'policies.view.viewable_policy.base_role.error' => 'Select a valid option for the base role.',
    'policies.view.viewable_policy.role' => 'Viewable Policy Role',
    'policies.view.viewable_policy.role.error' => 'Select a valid option for the policy role.',
    'policies.view.created' => 'View Policy Created Successfully',
    'policies.view.updated' => 'View Policy Updated Successfully',
    'policies.view.deleted' => 'View Policy Deleted Successfully',
    'policies.view.deleted.confirm' => 'Are you sure you wish to delete this View Policy?',

    'policies.view.fields' => 'Fields',
    'policies.view.viewable.employees' => 'Viewable by Employees',
    'policies.view.viewable.students' => 'Viewable by Students',
    'policies.view.viewable.parents' => 'Viewable by Parents',
    'policies.view.viewable.self' => 'Viewable by Self',
    'policies.view.editable' => 'Editable by Self',

    'relationships' => 'Relationships',
	'portrait' => 'Portrait',
    'name' => 'Name',
    'primary_roles' => 'Primary Role(s)',
    'add_new_person' => '<i class="fa fa-plus border-end pe-1 me-1"></i>Add New Person',
    'add_person' => 'Add Person',
    'are_you_trying_to_add_any_of_these_people' => 'Are you trying to add any of these people?',
    'is_a' => ':name is a',
    'update_reciprocal' => 'Update Reciprocal',
    'to2' => 'to :name',
    'update_relation' => 'Update Relation',
    'search_for_person' => 'Search for Person',
    'add_reciprocal' => 'Add Reciprocal',
    'establish_relation' => 'Establish Relation',
    'is_a_to' => ':name is a :expr to <a href=":route"> :name_2 </a>',
    'delete_relationship_oneway' => 'Delete relationship one-way',
    'delete_relationship_and_its_reciprocal' => 'Delete relationship and its reciprocal',
    'add_new_relationship' => 'Add New Relationship',
    'assign_roles_to_users' => 'Assign Roles to Users',
    'editing_roles' => 'Editing Roles',

    "school.directory" => "School Directory",
    "id" => "School ID|School IDs",
    "id.global" => "Global School Id|Global School Ids",
    "id.global.help" => "Global ID's mean that there will be a single ID for the entire school. All active users will use that ID as a default",
    "id.roles" => "School Id By Roles",
    "id.no.preview" => "No Id Preview Available",
    "id.roles.help" => "Role ID's mean that there will be a unique ID for each of the major roles in the school, Employees, Parents and Students. We don't make differentiations between different kind of employees, since there is simply too much overlap.",
    "id.student" => "Student School Id",
    "id.parent" => "Parent School Id",
    "id.employee" => "Employee School Id",
    "id.campuses" => "School Id By Campuses",
    "id.campuses.help" => "Campuses will create a unique ID for each of the campuses in the school. Multiple campuses will use the first one.",
    "id.campus" => ":campus School Id",
    "id.both.student" => "Student :campus School Id",
    "id.both.parent" => "Parent :campus School Id",
    "id.both.employee" => "Employee :campus School Id",
    "id.both" => "School Id By Campuses and Roles",
    "id.both.help" => "This option will create the most ID's. It will create an ID for each of the major roles in the school, for Employees, Parents and Students.",
    "id.manage" => "Manage School ID",
    "id.mine" => "My School ID",
    "id.add" => "Add School ID",
    "id.create" => "Create School ID",
    "id.name" => "School ID Name",
    "id.apply" => "Appply Id to people in the following criteria",
    "id.delete" => "Delete School ID",
    "id.delete.confirm" => "Are you sure you want to delete this School ID?",
    "id.edit" => "Edit School ID",
    "id.update" => "Update School ID",
    "id.outdated" => "Save Unsaved Changes",
    "id.uptodate" => "School ID is up to date",
    "id.revert" => "Revert Changes",
    'id.properties' => 'ID Card Properties',
    'id.dimensions' => 'Dimensions',
    'id.rows' => 'Rows',
    'id.columns' => 'Columns',
    'id.typography' => 'Typography',
    'id.typography.fs' => 'Font Size',
    'id.typography.ff' => 'Font Family',
    'id.typography.tc' => 'Text Color',
    'id.typography.i' => 'Italic',
    'id.typography.b' => 'Bold',
    'id.typography.u' => 'Underline',
    'id.typography.align.start' => 'Align Left',
    'id.typography.align.center' => 'Align Center',
    'id.typography.align.end' => 'Align Right',
    'id.text.shadow' => 'Text Shadow',
    'id.text.shadow.x' => 'Horizontal Offset',
    'id.text.shadow.y' => 'Vertical Offset',
    'id.text.shadow.blur' => 'Blur Radius',
    'id.text.shadow.color' => 'Shadow Color',


    'id.image.shadow' => 'Image Shadow',
    'id.image.shadow.x' => 'Horizontal Offset',
    'id.image.shadow.y' => 'Vertical Offset',
    'id.image.shadow.blur' => 'Blur Radius',
    'id.image.shadow.color' => 'Shadow Color',

    'id.no' => 'No School ID found for this person',

    'id.background' => 'Background',
    'id.background-color' => 'Background Color',
    'id.background-color-opacity' => 'Background Color Opacity',
    'id.background-blend' => 'Blend Mode',
    'id.background-blend.normal' => 'Normal',
    'id.background-blend.lighten' => 'Lighten',
    'id.background-blend.darken' => 'Darken',
    'id.background-blend.multiply' => 'Multiply',
    'id.background-blend.screen' => 'Screen',
    'id.background-blend.overlay' => 'Overlay',
    'id.background-blend.color-dodge' => 'Color Dodge',
    'id.background-blend.color-burn' => 'Color Burn',
    'id.background-blend.hard-light' => 'Hard Light',
    'id.background-blend.soft-light' => 'Soft Light',
    'id.background-blend.difference' => 'Difference',
    'id.background-blend.exclusion' => 'Eclusion',
    'id.background-blend.hue' => 'Hue',
    'id.background-blend.saturation' => 'Saturation',
    'id.background-blend.color' => 'Color',
    'id.background-blend.luminosity' => 'Luminosity',
    'id.background-image' => 'Background Image',
    'id.content-padding' => 'Content Padding',
    'id.image' => 'Image',
    'id.image.padding' => 'Image Padding',
    'id.image.margin' => 'Image Margin',
    'id.text.custom' => 'Custom Text',
    'id.year' => 'School Year',
    'id.person' => 'Person',
    'id.person.name' => 'Person Name',
    'id.student.grade' => 'Student Grade',
    'id.parents.children' => 'Parent Children',
    'id.parents.children.no' => 'No Children',
    'id.person.pic' => 'Person Picture',
    'id.barcode' => 'School ID Barcode',
    'id.barcode.settings' => 'Barcode',
    'id.barcode.type' => 'Barcode Type',
    'id.barcode.format' => 'Barcode Format',
    'id.barcode.width' => 'Barcode Width',
    'id.barcode.height' => 'Barcode Height',
    'id.barcode.scale.x' => 'Horizontal Scale',
    'id.barcode.scale.y' => 'Vertical Scale',
    'id.barcode.padding' => 'Barcode Padding',
    'id.barcode.color.background' => 'Background Color',
    'id.barcode.color.space' => 'Barcode Space Color',
    'id.barcode.color.bar' => 'Barcode Bar Color',
    'id.barcode.color' => 'Barcode Image Options',
    'id.barcode.text' => 'Barcode Text Options',
    'id.barcode.fs' => 'Barcode Text Size',
    'id.barcode.ff' => 'Barcode Text Font',
    'id.barcode.fc' => 'Barcode Text Color',




    'fields.roles' => 'Role Fields',
    'fields.types.text' => 'Text',
    'fields.types.textarea' => 'Notes',
    'fields.types.date' => 'Date',
    'fields.types.datetime' => 'Date-Time',
    'fields.types.select' => 'Drop Down',
    'fields.types.checkbox' => 'Check Box',
    'fields.types.email' => 'Email Input',
    'fields.types.url' => 'URL',
    'fields.types.radio' => 'Radio Button',

    'fields.permissions' => 'Field Permissions',
    'fields.permissions.select' => 'Select a Role to Manage Viewing Permissions',
    'fields.permissions.basic' => 'Basic Fields',

    'roles.field.select' => 'Select a Role to Manage Fields',
    'roles.select' => 'Select a Role',
    'roles.field.create' => 'Create Field',
    'roles.field.name' => 'Field Name',
    'roles.field.type' => 'Field Type',
    'roles.field.help' => 'Field Help',
    'roles.field.options' => 'Field Options',
    'roles.field.options.help' => 'Enter the option and click add to add it. Click the option in the existing options container to delete it.',
    'roles.field.placeholder' => 'Field Placeholder',
    'roles.field.add' => 'Add Field',
    'roles.field.preview' => 'Field Preview',
    'roles.field.existing' => 'Existing Fields',
    'roles.field.copy' => 'Copy This Field To|Copy All Fields To',

    'student' => 'Student|Students',
    'student.record' => 'Student Record|Student Records',
    'student.record.add' => 'Add Student Record|Add Student Records',
    'student.record.edit' => 'Edit Student Record|Edit Student Records',
    'student.record.manage' => 'Manage Student Records',
    'student.withdrawn' => 'Withdrawn',
    'student.withdraw' => 'Withdraw',
    'student.withdraw.reason' => 'Withdraw Reason',
    'student.withdraw.date' => 'Withdraw Date',
    'student.withdraw.notes' => 'Withdraw Notes',
    'student.withdraw.undo' => 'Undo Withdraw',
    'student.withdraw.reason.select' => 'Select a withdraw reason',

    'employee' => 'Employee|Employees',
    'staff' => 'Staff Member|Staff Members',
    'faculty' => 'Faculty Member|Faculty Members',
    'parent' => 'Parent|Parents',
    'coach' => 'Coach|Coaches',

    'name.creator.space' => 'Space After',
    'name.creator.basic' => 'Basic Field',
    'name.creator.role' => 'Role Field',
    'name.creator.text' => 'Text Field',
    'name.creator.sample' => 'Sample Name:',
    'name.creator.random' => 'New Random Person',
    'name.creator.reset' => 'Reset Name',

];
