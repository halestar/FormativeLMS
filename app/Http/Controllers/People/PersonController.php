<?php

namespace App\Http\Controllers\People;

use App\Classes\Settings\StorageSettings;
use App\Classes\Storage\DocumentFile;
use App\Enums\WorkStoragesInstances;
use App\Http\Controllers\Controller;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\SystemTables\Relationship;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PersonController extends Controller
{
    public static function middleware()
    {
        return ['auth'];
    }

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Person::class);
        $breadcrumb = [__('people.school.directory') => '#'];
	    $self = Auth::user();
        $currentYear = Year::currentYear();
        $itemsPerPageOptions = [10, 25, 50, 100];
        $requestedItemsPerPage = (int) $request->integer('items_per_page', (int) $self->getPreference('items_per_page', 10));
        $itemsPerPage = in_array($requestedItemsPerPage, $itemsPerPageOptions, true)
            ? $requestedItemsPerPage
            : 10;

        if ($request->has('items_per_page')) {
            $self->setPreference('items_per_page', $itemsPerPage);
        }

        $roleOptions = [
            '' => 'All roles',
            SchoolRoles::$STUDENT => __('common.student'),
            SchoolRoles::$PARENT => __('common.parent'),
            SchoolRoles::$EMPLOYEE => trans_choice('people.employee', 1),
            SchoolRoles::$FACULTY => SchoolRoles::$FACULTY,
            SchoolRoles::$STAFF => SchoolRoles::$STAFF,
            SchoolRoles::$SUBSTITUTE => SchoolRoles::$SUBSTITUTE,
        ];

        $selectedRole = $request->string('role')->toString();
        if (! array_key_exists($selectedRole, $roleOptions)) {
            $selectedRole = '';
        }

        $selectedCampus = $request->filled('campus')
            ? (int) $request->integer('campus')
            : null;
        $search = trim($request->string('search')->toString());

        $people = Person::query()
            ->with([
                'employeeCampuses',
                'substituteProfile.campuses',
                'studentRecords' => function ($query) use ($currentYear) {
                    $query->where('year_id', $currentYear->id)
                        ->whereNull('end_date')
                        ->with(['campus', 'level']);
                },
            ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($personQuery) use ($search) {
                    $personQuery->where('first', 'like', '%' . $search . '%')
                        ->orWhere('last', 'like', '%' . $search . '%')
                        ->orWhere('nick', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('school_id', 'like', '%' . $search . '%');
                });
            })
            ->when($selectedRole !== '', function ($query) use ($selectedRole, $currentYear) {
                match ($selectedRole) {
                    SchoolRoles::$STUDENT => $query->whereHas('studentRecords', function ($studentQuery) use ($currentYear) {
                        $studentQuery->where('year_id', $currentYear->id)
                            ->whereNull('end_date');
                    }),
                    SchoolRoles::$PARENT => $query->whereHas('relationships', function ($relationshipQuery) use ($currentYear) {
                        $relationshipQuery->wherePivot('relationship_id', Relationship::CHILD)
                            ->whereHas('studentRecords', function ($studentQuery) use ($currentYear) {
                                $studentQuery->where('year_id', $currentYear->id)
                                    ->whereNull('end_date');
                            });
                    }),
                    SchoolRoles::$SUBSTITUTE => $query->whereHas('substituteProfile'),
                    default => $query->role($selectedRole),
                };
            })
            ->when($selectedCampus, function ($query) use ($selectedCampus, $currentYear) {
                $query->where(function ($campusQuery) use ($selectedCampus, $currentYear) {
                    $campusQuery->whereHas('studentRecords', function ($studentQuery) use ($selectedCampus, $currentYear) {
                        $studentQuery->where('campus_id', $selectedCampus)
                            ->where('year_id', $currentYear->id)
                            ->whereNull('end_date');
                    })->orWhereHas('relationships', function ($relationshipQuery) use ($selectedCampus, $currentYear) {
                        $relationshipQuery->wherePivot('relationship_id', Relationship::CHILD)
                            ->whereHas('studentRecords', function ($studentQuery) use ($selectedCampus, $currentYear) {
                                $studentQuery->where('campus_id', $selectedCampus)
                                    ->where('year_id', $currentYear->id)
                                    ->whereNull('end_date');
                            });
                    })->orWhereHas('employeeCampuses', function ($employeeCampusQuery) use ($selectedCampus) {
                        $employeeCampusQuery->where('campuses.id', $selectedCampus);
                    })->orWhereHas('substituteProfile.campuses', function ($substituteCampusQuery) use ($selectedCampus) {
                        $substituteCampusQuery->where('campuses.id', $selectedCampus);
                    });
                });
            })
            ->paginate($itemsPerPage)
            ->withQueryString();

        $campusOptions = Campus::query()
            ->get(['id', 'name', 'abbr']);

        return view('people.index', compact(
            'breadcrumb',
            'people',
            'self',
            'campusOptions',
            'itemsPerPage',
            'itemsPerPageOptions',
            'roleOptions',
            'search',
            'selectedCampus',
            'selectedRole'
        ));
    }

    public function create()
    {
        Gate::authorize('create', Person::class);
        $breadcrumb = ['School Directory' => route('people.index'), 'Create New Person' => '#'];

        return view('people.create', compact('breadcrumb'));
    }

    public function show(Person $person)
    {
        Gate::authorize('view', $person);
        $breadcrumb =
            [
                __('people.school.directory') => route('people.index'),
                __('people.profile.view') => '#',
            ];
        if (Auth::user()->id == $person->id) {
            $breadcrumb = [__('people.profile.mine') => '#'];
        }
        $self = Auth::user();
        $isSelf = $self->id == $person->id;

        return view('people.show', compact('person', 'breadcrumb', 'self', 'isSelf'));
    }

    public function edit(Person $person)
    {
        Gate::authorize('edit', $person);
        $breadcrumb = [__('people.profile.view') => route('people.show', ['person' => $person->school_id]),
            __('people.profile.edit') => '#'];
        $self = Auth::user();
        $isSelf = $person->id == $self->id;
        if ($isSelf) {
            $breadcrumb = [__('people.profile.mine') => route('people.show', ['person' => $person->school_id]),
                __('people.profile.edit') => '#'];
        }
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
        // we should have the document file, either as the root, or the first element.
        $portrait = json_decode($request->input('portrait'), true);
        if (isset($portrait['school_id'])) {
            $doc = DocumentFile::hydrate($portrait);
        } else {
            $doc = DocumentFile::hydrate($portrait[0]);
        }
        // first, we persist the file, using the Person object as the filer.
        $connection = $storageSettings->getWorkConnection(WorkStoragesInstances::ProfileWork);
        $imgFile = $connection->persistFile($person, $doc, false);
        if ($imgFile) {
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
        foreach ($existingFields as $field) {
            $values[$field->fieldId] = $request->input($field->fieldId);
        }
        $person->schoolRoles()
            ->updateExistingPivot($role->id, ['field_values' => json_encode($values)]);

        return redirect(route('people.edit', ['person' => $person->school_id]))
            ->with('success-status', __('people.record.updated'));
    }

    public function roleFields()
    {
        Gate::authorize('has-permission', 'people.roles.fields');
        $breadcrumb = [__('people.fields.roles') => '#'];

        return view('people.fields', compact('breadcrumb'));
    }

    public function fieldPermissions()
    {
        Gate::authorize('has-permission', 'people.field.permissions');
        $breadcrumb =
            [
                __('system.menu.fields') => '#',
            ];

        return view('people.permissions', compact('breadcrumb'));
    }

    public function changeSelfPassword()
    {
        Gate::authorize('changeSelfPassword', Auth::user());
        $breadcrumb =
            [
                'Change Password' => '#',
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
