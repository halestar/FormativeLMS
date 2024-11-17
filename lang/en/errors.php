<?php

return
    [
        'permissions.name' => 'The permission name must be unique and less than 255 characters',
        'permissions.category' => 'The permission must belong to a category. Please select one from the list.',
        'permissions.description' => 'The description for a permission cannot be blank and must be less than 255 characters.',
        'roles.name' => 'The role name must be unique and less than 255 characters',
        'roles.permissions' => 'The role must contain at least one permission assigned to it',
	    'whoops_something_went_wrong' => 'Whoops! Something went wrong!',
        'campuses.name' => 'You must specify a campus name. Max 255 characters',
        'campuses.abbr' => 'You must specify a campus abbreviation. Max of 10 characters.',
        'campuses.levels' => 'You must select at least one level that this campus serves',
        'years.label' => 'You must provide a year label, (ie 2024-2025), max 255 characters',
        'years.start' => 'You must enter a date for the start of the year',
        'years.end' => 'You must enter a date for the end of the year',
        'terms.label' => 'You must provide a semester label, (ie Fall Semester), max 255 characters',
        'terms.campus_id' => 'You must select a campus for this semesters',
        'terms.start' => 'You must enter a date for the start semester between :start and :end',
        'terms.end' => 'You must enter a date for the end semester between :start and :end',
        'buildings.name' => 'You must provide a building name, max 255 characters',
        'buildings.areas' => 'You must select at least one building area that this building contains.',
        'rooms.name' => 'You must provide a room name, max 200 characters',
        'rooms.capacity' => 'You must provide a room max capacity',
        'rooms.campuses' => 'You must select at least one campus',
        'rooms.areas' => 'You must select the area the this room resides in.',
    ];
