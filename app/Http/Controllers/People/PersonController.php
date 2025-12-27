<?php

namespace App\Http\Controllers\People;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\DocumentFile;
use App\Enums\WorkStoragesInstances;
use App\Http\Controllers\Controller;
use App\Models\People\Person;
use App\Models\Utilities\MimeType;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PersonController extends Controller implements HasMiddleware
{
	public static function middleware()
	{
		return ['auth'];
	}
	
	public function index()
	{
		Gate::authorize('viewAny', Person::class);
		$breadcrumb = [__('people.school.directory') => "#"];
		$people = Person::paginate(10);
		$self = Auth::user();
		return view('people.index', compact('breadcrumb', 'people', 'self'));
	}
	
	public function create()
	{
		Gate::authorize('create', Person::class);
		$breadcrumb = ["School Directory" => route('people.index'), 'Create New Person' => "#"];
		return view('people.create', compact('breadcrumb'));
	}
	
	public function show(Person $person)
	{
		Gate::authorize('view', $person);
		$breadcrumb =
			[
				__('people.school.directory') => route('people.index'),
				__('people.profile.view') => "#"
			];
		if(Auth::user()->id == $person->id)
			$breadcrumb = [__('people.profile.mine') => "#"];
		$self = Auth::user();
		$isSelf = $self->id == $person->id;
		return view('people.show', compact('person', 'breadcrumb', 'self', 'isSelf'));
	}
	
	public function edit(Person $person)
	{
		Gate::authorize('edit', $person);
		$breadcrumb = [__('people.profile.view') => route('people.show', ['person' => $person->school_id]),
		               __('people.profile.edit') => "#"];
		$self = Auth::user();
		$isSelf = $person->id == $self->id;
		if($isSelf)
			$breadcrumb = [__('people.profile.mine') => route('people.show', ['person' => $person->school_id]),
			               __('people.profile.edit') => "#"];
		Log::debug(print_r($person->portrait_url, true));
		return view('people.edit', compact('person', 'breadcrumb', 'self', 'isSelf'));
	}
	
	public function updateBasic(Request $request, Person $person)
	{
		Gate::authorize('edit', $person);
		$person->fill($request->all());
		$person->save();
		return redirect(route('people.show', ['person' => $person->school_id]))
			->with('success-status', __('people.registered.updated'));
	}
	
	public function updatePortrait(Request $request, Person $person, StorageSettings $storageSettings)
	{
		Gate::authorize('edit', $person);
		//we should have the document file, either as the root, or the first element.
		$portrait = json_decode($request->input('portrait'), true);
		if(isset($portrait['school_id']))
			$doc = DocumentFile::hydrate($portrait);
		else
			$doc = DocumentFile::hydrate($portrait[0]);
		//first, we persist the file, using the Person object as the filer.
		$connection = $storageSettings->getWorkConnection(WorkStoragesInstances::ProfileWork);
		$imgFile = $connection->persistFile($person, $doc, false);
		if($imgFile)
		{
			$person->portrait_url->useWorkfile($imgFile);
			$person->save();
		}
		return redirect(route('people.edit', ['person' => $person->school_id]))
			->with('success-status', __('people.registered.updated'));
	}
	
	public function updateRoleFields(Request $request, Person $person, SchoolRoles $role)
	{
		Gate::authorize('edit', $person);
		$values = [];
		$existingFields = $role->fields;
		foreach($existingFields as $field)
			$values[$field->fieldId] = $request->input($field->fieldId);
		$person->schoolRoles()
		       ->updateExistingPivot($role->id, ['field_values' => json_encode($values)]);
		return redirect(route('people.edit', ['person' => $person->school_id]))
			->with('success-status', __('people.record.updated'));
	}
	
	public function deletePortrait(Request $request, Person $person)
	{
		Gate::authorize('edit', $person);
		$person->portrait_url->remove();
		$person->save();
		return redirect(route('people.edit', ['person' => $person->school_id]))
			->with('success-status', __('people.record.updated'));
	}
	
	public function roleFields()
	{
		Gate::authorize('has-permission', 'people.roles.fields');
		$breadcrumb = [__('people.fields.roles') => "#"];
		return view('people.fields', compact('breadcrumb'));
	}
	
	public function fieldPermissions()
	{
		Gate::authorize('has-permission', 'people.field.permissions');
		$breadcrumb =
			[
				__('system.menu.fields') => '#'
			];
		return view('people.permissions', compact('breadcrumb'));
	}
	
	public function changeSelfPassword()
	{
		Gate::authorize('changeSelfPassword', Auth::user());
		$breadcrumb =
			[
				"Change Password" => '#'
			];
		$person = Auth::user();
		return view('people.password', compact('breadcrumb', 'person'));
	}
	
	private function errors(): array
	{
		return
			[
				'last' => __('people.profile.fields.last.error'),
			];
	}
}
