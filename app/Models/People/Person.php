<?php

namespace App\Models\People;

use App\Casts\LogItem;
use App\Casts\People\Portrait;
use App\Classes\Integrators\IntegrationsManager;
use App\Classes\People\RoleField;
use App\Classes\Settings\SchoolSettings;
use App\Enums\ClassViewer;
use App\Enums\IntegratorServiceTypes;
use App\Interfaces\HasSchoolRoles;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Integrations\IntegrationService;
use App\Models\Locations\Campus;
use App\Models\Locations\Term;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use App\Models\SubjectMatter\Learning\LearningDemonstrationTemplate;
use App\Models\SubjectMatter\SchoolClass;
use App\Models\SystemTables\Relationship;
use App\Models\Utilities\SchoolMessage;
use App\Models\Utilities\SchoolRoles;
use App\Notifications\Classes\NewClassMessageNotification;
use App\Traits\Addressable;
use App\Traits\Campuseable;
use App\Traits\HasFullTextSearch;
use App\Traits\HasLogs;
use App\Traits\HasSchoolRolesTrait;
use App\Traits\Phoneable;
use Auth;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;


class Person extends Authenticatable implements HasSchoolRoles
{
	use HasFactory, HasLogs, SoftDeletes, HasSchoolRolesTrait, Phoneable, Addressable, Notifiable, HasFullTextSearch,
		Campuseable, Impersonate, HasApiTokens;

    /************************************************************************************************************
     * TABLE DEFINITIONS
     */
	public const UKN_IMG = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>';
	protected static string $logField = 'global_log';
	public $timestamps = true;
	public $incrementing = true;
	protected $with = ['schoolRoles'];
	protected $table = "people";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'first',
			'middle',
			'last',
			'nick',
			'email',
			'dob',
            'prefs',
			'portrait_url',
			'thumbnail_url',
		];
	protected $hidden = [
		'remember_token',
	];

    /************************************************************************************************************
     * MODEL OVERRIDES
     */
	
	protected static function booted(): void
	{
		static::addGlobalScope('name_order', function(Builder $builder)
		{
			$builder->orderBy('last')
			        ->orderBy('first');
		});
		static::creating(function(Person $person)
		{
			$person->school_id = time();
		});
		static::created(function(Person $person)
		{
			$hashids = new Hashids('FabLMS', config('lms.school_id_length'), '0123456789cfhistu');
			$person->school_id = $hashids->encode($person->id);
			$person->save();
		});
	}
	
	public function getRouteKeyName(): string
	{
		return 'school_id';
	}
	
	public function receivesBroadcastNotificationsOn(): string
	{
		return 'people.' . $this->id;
	}

    protected function casts(): array
    {
        return
            [
                'dob' => 'date: m/d/y',
                'global_log' => LogItem::class,
                'prefs' => 'array',
                'portrait_url' => Portrait::class,
                'created_at' => 'datetime: m/d/Y h:i A',
                'updated_at' => 'datetime: m/d/Y h:i A',
            ];
    }

    public function __toString()
    {
        return $this->name;
    }

    /************************************************************************************************************
     * ROLE FUNCTIONS
     */

    public function isStudent(): bool
    {
        return $this->hasRole(SchoolRoles::$STUDENT);
    }

    public function isEmployee(): bool
    {
        return Cache::remember('person-is-employee-' . $this->id, 3600, fn() => $this->hasRole(SchoolRoles::$EMPLOYEE));
    }

    public function isParent(): bool
    {
        return Cache::remember('person-is-parent-' . $this->id, 3600, fn() => $this->hasRole(SchoolRoles::$PARENT));
    }

    public function isTeacher(): bool
    {
        return Cache::remember('isTeacher-' . $this->id, 60 * 60 * 24, function () {
            return $this->hasRole(SchoolRoles::$FACULTY);
        });
    }


    /************************************************************************************************************
	 * Mutators/Accessors
	 */

    protected function name(): Attribute
	{
		return Attribute::make
		(
			get: function(mixed $value, array $attributes)
			{
                return Cache::remember('person-name-' . $this->id, 3600, function()
                {
                    $settings = app(SchoolSettings::class);
                    if ($this->isStudent())
                        $name = $settings->studentName->applyName($this);
                    elseif ($this->isEmployee())
                        $name = $settings->employeeName->applyName($this);
                    elseif ($this->isParent())
                        $name = $settings->parentName->applyName($this);
                    else
                        $name = $this->first . " " . $this->last;
                    return $name;
                });
			}
		);
	}
	

    protected function systemEmail(): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) => Cache::remember('system-email-' . $this->id, 3600, fn() => $attributes['email']),
		);
	}
	
	protected function preferredFirst(): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) => Cache::remember('preferred-first-' . $this->id, 3600, fn() => $attributes['nick'] ?? $attributes['first']),
		);
	}

    protected function dob(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) => Cache::remember('dob-' . $this->id, 3600, fn() => Carbon::parse($value)),
        );
    }

    protected function thumbnailUrl(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) => Cache::remember('thumbnail-url-' . $this->id, 3600, fn() => $attributes['thumbnail_url']?? Person::UKN_IMG),
        );
    }

    protected function schoolId(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) => Cache::remember('school-id-' . $this->id, 3600, fn() => str_pad($value, 10, '0', STR_PAD_LEFT)),
        );
    }

    /************************************************************************************************************
     * BOOLEAN FUNCTIONS
     */

    public function hasPortrait(): bool
    {
        return ($this->attributes['portrait_url'] != null && $this->attributes['portrait_url'] != '');
    }
    public function hasChildren(): bool
    {
        return Cache::remember('hasChildren-' . $this->id, 60 * 60 * 24, function () {
            return $this->relationships()
                    ->where('relationship_id', Relationship::CHILD)
                    ->count() > 0;
        });
    }

    public function isParentOfPerson(Person $target): bool
    {
        return $target->relationships()
                ->where('to_person_id', $this->id)
                ->where('relationship_id', Relationship::CHILD)
                ->count() > 0;
    }

    public function canUseAi(): bool
    {
        return Cache::remember('can-use-ai' . $this->id, 0,  function()
        {
            $intManager = app(IntegrationsManager::class);
            return $intManager->hasPersonalConnection($this, IntegratorServiceTypes::AI);
        });
    }

	public function isTrackingStudent(StudentRecord $student): bool
	{
		return $this->studentTrackee()->where('student_id', $student->id)->exists();
	}


    /************************************************************************************************************
     * VIEW PERMISSIONS
     */
	
	public function canViewField(RoleField|string $testingField, Person $target): bool
	{
		if($this->can('people.view'))
			return true;
		if($target->id == $this->id)
		{
			if($testingField instanceof RoleField)
				return $this->selfViewableFields()
				            ->where('field', '=', $testingField->fieldId)
				            ->where('role_id', '=', $testingField->roleId)
				            ->count() > 0;
			else
				return $this->selfViewableFields()
				            ->where('field', '=', $testingField)
				            ->where('role_id', '=', '')
				            ->count() > 0;
		}
		else
		{
			if($testingField instanceof RoleField)
				return $this->viewableFields()
				            ->where('field', '=', $testingField->fieldId)
				            ->where('role_id', '=', $testingField->roleId)
				            ->count() > 0;
			else
				return $this->viewableFields()
				            ->where('field', '=', $testingField)
				            ->where('role_id', '=', '')
				            ->count() > 0;
		}
	}
	
	public function selfViewableFields(): Collection
	{
		return FieldPermission::where('by_self', true)
		                      ->get();
	}
	
	public function viewableFields(): Collection
	{
		$query = FieldPermission::where('editable', '>', 10);
		if($this->isEmployee())
			$query = $query->orWhere('by_employees', true);
		if($this->isStudent())
			$query = $query->orWhere('by_students', true);
		if($this->isParent())
			$query = $query->orWhere('by_parents', true);
		return $query->get();
	}

    public function canEditOwnField(RoleField|string $testingField): bool
    {
        if($this->can('people.edit'))
            return true;
        if($testingField instanceof RoleField)
        {
            return $this->editableFields()
                    ->where('field', '=', $testingField->fieldId)
                    ->where('role_id', '=', $testingField->roleId)
                    ->count() > 0;
        }
        else
            return $this->editableFields()
                    ->where('field', '=', $testingField)
                    ->where('role_id', '=', '')
                    ->count() > 0;
    }

    public function editableFields(): Collection
    {
        return FieldPermission::where('editable', true)
            ->get();
    }

    public function classViewRole(ClassSession $session): ?ClassViewer
    {
        $person = $this;
        return Cache::remember('class_view_role_' . $this->id . '_' . $session->id, 0, function() use ($session, $person)
        {
            return ClassViewer::determineType($person, $session);
        });
    }


    /************************************************************************************************************
     * DB RELATIONSHIPS
     */

    public function employeeCampuses(): MorphToMany
    {
        return $this->campuses();
    }

    public function classMessages(): HasMany
    {
        return $this->hasMany(ClassMessage::class, 'person_id');
    }

    public function studentTrackee(): BelongsToMany
    {
        return $this->belongsToMany(StudentRecord::class, 'student_trackers', 'person_id', 'student_id');
    }

    public function authConnection(): BelongsTo
    {
        return $this->belongsTo(IntegrationConnection::class, 'auth_connection_id');
    }
	
	public function relationships(): BelongsToMany
	{
		return $this->belongsToMany(Person::class, "people_relations", "from_person_id", "to_person_id")
		            ->as('personal')
		            ->using(PersonalRelations::class)
		            ->withPivot(
			            [
				            'relationship_id',
			            ]);
	}

    public function schoolMessageSubscriptions(): BelongsToMany
    {
        return $this->belongsToMany(SchoolMessage::class, 'school_messages_subscriptions', 'person_id', 'message_id');
    }

    public function studentRecords(): HasMany
    {
        return $this->hasMany(StudentRecord::class, 'person_id');
    }

    public function parents(): BelongsToMany
    {
        return $this->relationships()
            ->wherePivot('relationship_id', Relationship::CHILD);
    }

    public function allChildren(): BelongsToMany
    {
        return $this->relationships()
            ->wherePivot('relationship_id', Relationship::CHILD);
    }

    public function connectedServices(): BelongsToMany
    {
        return $this->belongsToMany(IntegrationService::class, 'integration_connections', 'person_id', 'service_id')
            ->withPivot('id', 'data', 'enabled', 'className')
            ->as('lms_service_connection')
            ->using(IntegrationConnection::class);
    }

    public function currentClassSessions(): BelongsToMany
    {
        return $this->belongsToMany(ClassSession::class, 'class_sessions_teachers', 'person_id', 'session_id')
            ->join('terms', 'terms.id', '=', 'class_sessions.term_id')
            ->whereBetweenColumns(DB::raw(date("'Y-m-d'")), ['terms.term_start', 'terms.term_end']);
    }

    public function classesTaught(): BelongsToMany
    {
        return $this->belongsToMany(ClassSession::class, 'class_sessions_teachers', 'person_id', 'session_id');
    }

    public function learningDemonstrationTemplates(): HasMany
    {
        return $this->hasMany(LearningDemonstrationTemplate::class, 'person_id');
    }

    /************************************************************************************************************
     * ADRESSES
     */
	public function primaryAddress(): Address
	{
		return $this->addresses()
		            ->wherePivot('primary', true)
		            ->first();
	}

    /************************************************************************************************************
     * PHONES
     */
	public function primaryPhone(): Phone
	{
		return $this->phones()
		            ->wherePivot('primary', true)
		            ->first();
	}

    /************************************************************************************************************
     * STUDENT FUNCTIONS
     */
	public function student(): ?StudentRecord
	{
		$year = Year::currentYear();
		return $this->studentRecords()
		            ->where('year_id', $year->id)
		            ->whereNull('end_date')
		            ->first();
	}
	

	
	public function studentInTerm(Term $term): ?StudentRecord
	{
		return $this->studentRecords()
		            ->where('year_id', $term->year_id)
		            ->whereNull('end_date')
		            ->first();
	}
	
	public function studentInYear(Year $year): ?StudentRecord
	{
		return $this->studentRecords()
		            ->where('year_id', $year->id)
		            ->whereNull('end_date')
		            ->first();
	}

    /************************************************************************************************************
     * PARENT FUNCTIONS
     */
	public function currentChildStudents(): ?Collection
	{
		$currentYear = Year::currentYear();
		return StudentRecord::select('student_records.*')
		                    ->join('people', 'people.id', '=', 'student_records.person_id')
		                    ->join('people_relations', 'people_relations.from_person_id', '=', 'people.id')
		                    ->where('people_relations.to_person_id', $this->id)
		                    ->where('student_records.year_id', $currentYear->id)
		                    ->whereNull('student_records.end_date')
		                    ->where('people_relations.relationship_id', Relationship::CHILD)
		                    ->get();
	}
	
	public function parentCampuses(): ?Collection
	{
		$currentYear = Year::currentYear();
		return Campus::select('campuses.*')
		             ->join('student_records', 'student_records.campus_id', '=', 'campuses.id')
		             ->join('people', 'people.id', '=', 'student_records.person_id')
		             ->join('people_relations', 'people_relations.from_person_id', '=', 'people.id')
		             ->where('people_relations.to_person_id', $this->id)
		             ->where('student_records.year_id', $currentYear->id)
		             ->whereNull('student_records.end_date')
		             ->where('people_relations.relationship_id', Relationship::CHILD)
		             ->groupBy('campuses.id')
		             ->get();
	}
	
	public function allStudentRecords(): Collection
	{
		return StudentRecord::select('student_records.*')
		                    ->join('people', 'people.id', '=', 'student_records.person_id')
		                    ->join('people_relations', 'people_relations.from_person_id', '=', 'people.id')
		                    ->where('people_relations.relationship_id', Relationship::CHILD)
		                    ->where('people_relations.to_person_id', $this->id)
		                    ->get();
	}


    /************************************************************************************************************
     * TEACHER FUNCTIONS
     */
	
	public function currentSchoolClasses(): Collection
	{
		return SchoolClass::select('school_classes.*')
			->join('class_sessions', 'class_sessions.class_id', '=', 'school_classes.id')
			->join('terms', 'terms.id', '=', 'class_sessions.term_id')
			->join('class_sessions_teachers', 'class_sessions_teachers.session_id', '=', 'class_sessions.id')
			->join('courses', 'courses.id', '=', 'school_classes.course_id')
			->whereBetweenColumns(DB::raw(date("'Y-m-d'")), ['terms.term_start', 'terms.term_end'])
			->where('class_sessions_teachers.person_id', $this->id)
			->orderBy('courses.name')
			->groupBy('school_classes.id')
			->get();
	}
	
	public function teachesClassSession(ClassSession $session): bool
	{
		return $this->classesTaught()
		            ->where('class_sessions.id', $session->id)
		            ->exists();
	}


    /************************************************************************************************************
     * NOTIFICATIONS
     */
    public function lmsNotifications()
    {
        return $this->notifications()
            ->where('type', 'lms-notification');
    }
	
	public function classMessageNotifications()
	{
		return $this->notifications()
		            ->where('type', 'class-message');
	}

    /************************************************************************************************************
     * INTEGRATORS
     */
	
	public function getIntegrationServices(IntegratorServiceTypes $type = null): Collection
	{
		if(!$type)
			return $this->connectedServices;
		return $this->connectedServices()
		            ->where('service_type', $type)
		            ->get();
	}
	
	public function removeIntegrationService(IntegrationService $service): void
	{
		if($this->hasIntegrationService($service))
			$this->connectedServices()
			     ->detach($service->id);
	}

	public function hasIntegrationService(IntegrationService $service): bool
	{
		return $this->connectedServices()
		            ->where('service_id', $service->id)
		            ->exists();
	}
	
	public function getServiceConnection(IntegrationService $service): ?IntegrationConnection
	{
		return $this->connectedServices()
		            ->where('service_id', $service->id)
		            ->first()?->lms_service_connection;
	}

    /************************************************************************************************************
     * ADMIN FUNCTIONS
     */
	
	public function canImpersonate()
	{
		return $this->can('people.impersonate');
	}
	
	public function canBeImpersonated()
	{
		return !$this->hasRole(SchoolRoles::$ADMIN) && ($this->id != auth()->user()->id);
	}

    /************************************************************************************************************
     * PREFERENCES
     */
    public function getPreference(string $key, mixed $default = null): mixed
    {
        //if the prefs are empty, init them.
        if(!$this->prefs || !is_array($this->prefs) || count($this->prefs) == 0)
            $this->prefs = config('lms.prefs_default', []);
        //we assume the key is in dotted notation.
        $keys = explode('.', $key);
        if(!$key || count($keys) == 0)
            return $default;
        if(count($keys) == 1)
            return $this->prefs[$key] ?? $default;
        $data = $this->prefs;
        $pointer = &$data;
        foreach($keys as $i => $k)
        {
            //is this the last one?
            if($i == count($keys) - 1)
                return $pointer[$k] ?? $default;
            //is it set?
            if(!isset($pointer[$k]) || !is_array($pointer[$k]))
                return $default;
            $pointer = &$pointer[$k];
        }
        return $default;
    }

    public function setPreference(string $key, mixed $value): void
    {
        //if the prefs are empty, init them.
        if(!$this->prefs || !is_array($this->prefs) || count($this->prefs) == 0)
            $this->prefs = config('lms.prefs_default', []);
        //we assume the key is in dotted notation.
        $keys = explode('.', $key);
        if(!$key || count($keys) == 0)
            return;
        $data = $this->prefs;
        if(count($keys) == 1)
            $data[$key] = $value;
        else
        {
            $pointer = &$data;
            foreach($keys as $i => $k)
            {
                if($i == count($keys) - 1)
                    $pointer[$k] = $value;
                else
                {
                    if(!isset($pointer[$k]) || !is_array($pointer[$k]))
                        $pointer[$k] = [];
                }
                $pointer = &$pointer[$k];
            }
        }

        $this->prefs = $data;
    }

    /************************************************************************************************************
     * SCOPES
     */
	#[Scope]
	protected function teachers(Builder $query): void
	{
		$query->whereHas('schoolRoles', function(Builder $query)
		{
			$query->where('name', SchoolRoles::$FACULTY);
		});
	}
	
	#[Scope]
	protected function students(Builder $query): void
	{
		$query->whereHas('schoolRoles', function(Builder $query)
		{
			$query->where('name', SchoolRoles::$STUDENT);
		});
	}
	
	#[Scope]
	protected function staff(Builder $query): void
	{
		$query->whereHas('schoolRoles', function(Builder $query)
		{
			$query->where('name', SchoolRoles::$STAFF);
		});
	}
	
	#[Scope]
	protected function childParents(Builder $query): void
	{
		$query->whereHas('schoolRoles', function(Builder $query)
		{
			$query->where('name', SchoolRoles::$PARENT);
		});
	}
	
	#[Scope]
	protected function coaches(Builder $query): void
	{
		$query->whereHas('schoolRoles', function(Builder $query)
		{
			$query->where('name', SchoolRoles::$COACH);
		});
	}


	
}
