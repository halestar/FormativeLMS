<?php

use App\Models\People\Person;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('people.{id}', function (Person $user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('enrollment', function (Person $user)
{
    return $user->can('classes.enrollment');
});

