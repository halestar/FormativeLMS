<?php

namespace App\Http\Controllers\ClassManagement;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class StudentEnrollmentController extends Controller implements HasMiddleware
{
	public static function middleware()
	{
		return ['auth'];
	}
	
	private static function errors(): array
	{
		return [
			'name' => __('errors.subjects.name'),
		];
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
