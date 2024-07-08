<?php

return
    [
        'permissions.name' => 'The permission name must be unique and less than 255 characters',
        'permissions.category' => 'The permission must belong to a category. Please select one from the list.',
        'permissions.description' => 'The description for a permission cannot be blank and must be less than 255 characters.',
        'roles.name' => 'The role name must be unique and less than 255 characters',
        'roles.permissions' => 'The role must contain at least one permission assigned to it',
    ];
