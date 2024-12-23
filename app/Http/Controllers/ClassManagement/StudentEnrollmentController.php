<?php

namespace App\Http\Controllers\ClassManagement;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class StudentEnrollmentController extends Controller
{
    private static function errors(): array
    {
        return [
            'name' => __('errors.subjects.name'),
        ];
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function general()
    {
        Gate::authorize('has-permission', 'classes.enrollment');
        $breadcrumb =
            [
                __('system.menu.classes.enrollment.general') => "#",
            ];
        return view('subjects.enrollment.general', compact('breadcrumb'));
    }
}
