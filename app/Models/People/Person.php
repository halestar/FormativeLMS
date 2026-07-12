<?php

namespace App\Models\People;

use App\Casts\People\Portrait;
use App\Classes\Integrators\IntegrationsManager;
use App\Classes\People\RoleFields;
use App\Classes\Settings\AiSettings;
use App\Classes\Settings\SchoolSettings;
use App\Enums\ClassViewer;
use App\Enums\IntegratorServiceTypes;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Interfaces\HasCampuses;
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
use App\Models\SubjectMatter\Subject;
use App\Models\Substitutes\Substitute;
use App\Models\Substitutes\SubstituteClassRequest;
use App\Models\Substitutes\SubstituteRequest;
use App\Models\SystemTables\Relationship;
use App\Models\Utilities\SchoolMessage;
use App\Models\Utilities\SchoolRoles;
use App\Models\Utilities\WorkFile;
use App\Traits\Addressable;
use App\Traits\Campuseable;
use App\Traits\HasLogs;
use App\Traits\HasSchoolRolesTrait;
use App\Traits\HasWorkFiles;
use App\Traits\Phoneable;
use Hashids\Hashids;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys;
use Spatie\LaravelPasskeys\Models\Concerns\InteractsWithPasskeys;

class Person extends Authenticatable implements Fileable, HasCampuses, HasPasskeys, HasSchoolRoles
{
	use Addressable, Campuseable, HasApiTokens, HasFactory, HasLogs, HasSchoolRolesTrait,
		HasWorkFiles, Impersonate, InteractsWithPasskeys, Notifiable, Phoneable, Searchable, SoftDeletes;

	/************************************************************************************************************
	 * TABLE DEFINITIONS
	 */
	public const UKN_IMG = 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>';
	public $timestamps = true;
	public $incrementing = true;
	protected $with = ['schoolRoles'];
	protected $table = 'people';
	protected $primaryKey = 'id';
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
		];

	protected $hidden = [
		'remember_token',
		'mfa_secret',
	];

	public ?RoleFields $roleFieldsProxy = null;

	/************************************************************************************************************
	 * MODEL OVERRIDES
	 */

	protected static function booted(): void
	{
		static::addGlobalScope('name_order', function (Builder $builder)
		{
			$builder->orderBy('last')
			        ->orderBy('first');
		});
		static::creating(function (Person $person)
		{
			$person->school_id = time();
		});
		static::created(function (Person $person)
		{
			$hashids = new Hashids('FabLMS', config('lms.school_id_length'), '0123456789cfhistu');
			$person->school_id = $hashids->encode($person->id);
			$person->save();
		});
		static::updated(function (Person $person)
		{
			Log::info('Flushing cache for person ' . $person->id);
			Cache::tags('person-' . $person->id)->flush();
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
				'dob'             => 'date: m/d/y',
				'prefs'           => 'array',
				'portrait_url'    => Portrait::class,
				'mfa_enabled'     => 'boolean',
				'mfa_secret'      => 'encrypted',
				'mfa_verified_at' => 'date',
				'created_at'      => 'datetime: m/d/Y h:i A',
				'updated_at'      => 'datetime: m/d/Y h:i A',
			];
	}

	public function __toString()
	{
		return $this->name;
	}

	public function toSearchableArray(): array
	{
		return
			[
				'id'        => $this->id,
				'first'     => $this->first,
				'middle'    => $this->middle,
				'last'      => $this->last,
				'email'     => $this->email,
				'nick'      => $this->nick,
				'dob'       => $this->dob?->format('m/d/y') ?? null,
				'school_id' => $this->school_id,
				'roles'     => $this->roles->pluck('name')->toArray(),
				'addresses' => $this->addresses->map(fn (Address $address) => $address->personal->prettyAddress())
				                               ->toArray(),
				'phones'    => $this->phones->map(fn (Phone $phone) => $phone->personal->prettyPhone())->toArray(),
				'campuses'  => $this->campuses->map(fn (Campus $campus) => $campus->name . ' (' . $campus->abbr . ')')
				                              ->toArray(),
			];
	}

	public function save(array $options = [])
	{
		$proxyIsDirty = $this->roleFieldsProxy?->isDirty() ?? false;
		$saved = parent::save($options);
		if ($proxyIsDirty)
		{
			$this->roleFieldsProxy->save();
			return true;
		}
		return $saved;
	}

	public function fill(array $attributes)
	{
		foreach ($attributes as $key => $value)
		{
			if (!str_starts_with($key, 'role_fields'))
				continue;
			$key = substr($key, strlen('role_fields.'));
			$this->role_fields->{$key} = $value;
			unset($attributes[$key]);
		}
		return parent::fill($attributes);
	}

	/************************************************************************************************************
	 * ROLE FUNCTIONS
	 */

	public function isStudent(): bool
	{
		return Cache::tags(
			[
				'people',
				'person-' . $this->id,
				'person-roles',
			])->rememberForever('person_is_student_' .
		                        $this->id, fn () => $this->hasRole(SchoolRoles::$STUDENT));
	}

	public function isEmployee(): bool
	{
		return Cache::tags(
			[
				'people',
				'person-' . $this->id,
				'person-roles',
			])->rememberForever('person_is_employee_' .
		                        $this->id, fn () => $this->hasRole(SchoolRoles::$EMPLOYEE));
	}

	public function isParent(): bool
	{
		return Cache::tags(
			[
				'people',
				'person-' . $this->id,
				'person-roles',
			])->rememberForever('person_is_parent_' .
		                        $this->id, fn () => $this->hasRole(SchoolRoles::$PARENT));
	}

	public function isTeacher(): bool
	{
		return Cache::tags(
			[
				'people',
				'person-' . $this->id,
				'person-roles',
			])->rememberForever('person_is_teacher_' .
		                        $this->id, fn () => $this->hasRole(SchoolRoles::$FACULTY));
	}

	public function isSubstitute(): bool
	{
		return Cache::tags(
			[
				'people',
				'person-' . $this->id,
				'person-roles',
			])->rememberForever('person_is_substitute_' .
		                        $this->id, fn () => $this->hasRole([
			SchoolRoles::$SUBSTITUTE, SchoolRoles::$OLD_SUBSTITUTE,
		]));
	}

	/************************************************************************************************************
	 * Mutators/Accessors
	 */

	protected function name(): Attribute
	{
		return Attribute::make(
			get: function (mixed $value, array $attributes)
			{
				return Cache::tags(
					[
						'people',
						'person-' . $this->id,
					])->rememberForever('person-name-' . $this->id, function ()
				{
					$settings = app(SchoolSettings::class);
					if ($this->isStudent())
						$name = $settings->studentName->applyName($this);
					elseif ($this->isEmployee())
						$name = $settings->employeeName->applyName($this);
					elseif ($this->isParent())
						$name = $settings->parentName->applyName($this);
					else
						$name = $this->nick ?: ($this->first . ' ' . $this->last);

					return $name;
				});
			}
		);
	}

	public function roleFields(): Attribute
	{
		return Attribute::make(
			get: function ()
			{
				if (!$this->roleFieldsProxy)
					$this->roleFieldsProxy = new RoleFields($this);
				return $this->roleFieldsProxy;
			});
	}


	/************************************************************************************************************
	 * BOOLEAN FUNCTIONS
	 */

	public function hasPortrait(): bool
	{
		return $this->attributes['portrait_url'] != null && $this->attributes['portrait_url'] != '' &&
		       $this->attributes['portrait_url'] != asset('images/unk.svg');
	}

	public function hasChildren(): bool
	{
		return Cache::tags(
			[
				'people',
				'person-' . $this->id,
				'person-relationships',
			])->rememberForever('hasChildren-' . $this->id, function ()
		{
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
		// easiest solution is if they have the system.ai permission
		if ($this->can('system.ai'))
		{
			return true;
		}
		$aiSettings = app(AiSettings::class);
		$intManager = app(IntegrationsManager::class);
		// first, check the settings to see if users can use the system AI
		if ($aiSettings->allow_global_ai)
		{
			return $intManager->hasSystemConnection(IntegratorServiceTypes::AI);
		}
		elseif ($aiSettings->allow_user_ai)
		{
			return $intManager->hasPersonalConnection($this, IntegratorServiceTypes::AI);
		}

		return false;
	}

	public function isTrackingStudent(StudentRecord $student): bool
	{
		return $this->studentTrackee()->where('student_id', $student->id)->exists();
	}

	/************************************************************************************************************
	 * VIEW PERMISSIONS
	 */

	public function classViewRole(ClassSession $session): ?ClassViewer
	{
		$cacheTags =
			[
				'class-view-role',
				'person- ' . $this->id,
				'session-' . $session->id,
			];
		return Cache::tags($cacheTags)->rememberForever('class_view_role_' . $this->id . '_' .
		                                                $session->id, function () use ($session)
		{
			return ClassViewer::determineType($this, $session);
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
		return $this->belongsToMany(Person::class, 'people_relations', 'from_person_id', 'to_person_id')
		            ->as('personal')
		            ->using(PersonalRelations::class)
		            ->withPivot(
			            [
				            'relationship_id',
			            ]
		            );
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
		            ->whereHas('term', function (Builder $query)
		            {
			            $query->whereBetweenColumns(DB::raw(date("'Y-m-d'")),
				            [
					            'terms.term_start',
					            'terms.term_end',
				            ]);
		            });
	}

	public function classesTaught(): BelongsToMany
	{
		return $this->belongsToMany(ClassSession::class, 'class_sessions_teachers', 'person_id', 'session_id');
	}

	public function learningDemonstrationTemplates(): HasMany
	{
		return $this->hasMany(LearningDemonstrationTemplate::class, 'person_id');
	}

	public function substituteProfile(): HasOne
	{
		return $this->hasOne(Substitute::class, 'person_id');
	}

	public function subjectsTaught(): BelongsToMany
	{
		return $this->belongsToMany(Subject::class, 'subjects_teachers', 'person_id', 'subject_id');
	}

	/************************************************************************************************************
	 * ADRESSES
	 */
	public function primaryAddress(): Attribute
	{
		return Attribute::make
		(
			get: fn () => $this->addresses()
			                   ->wherePivot('primary', true)
			                   ->first(),
		);
	}

	/************************************************************************************************************
	 * PHONES
	 */
	public function primaryPhone(): Attribute
	{
		return Attribute::make
		(
			get: fn () => $this->phones()
			                   ->wherePivot('primary', true)
			                   ->first(),
		);
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

	public function numStudentChildren(): int
	{
		$cacheTags =
			[
				'num-children',
				'person-' . $this->id,
			];
		return Cache::tags($cacheTags)->rememberForever('num_children_' . $this->id, function ()
		{
			$currentYear = Year::currentYear();

			return StudentRecord::select('student_records.*')
			                    ->join('people', 'people.id', '=', 'student_records.person_id')
			                    ->join('people_relations', 'people_relations.from_person_id', '=', 'people.id')
			                    ->where('people_relations.to_person_id', $this->id)
			                    ->where('student_records.year_id', $currentYear->id)
			                    ->whereNull('student_records.end_date')
			                    ->where('people_relations.relationship_id', Relationship::CHILD)
			                    ->count();
		});
	}

	public function viewingStudent(): BelongsTo
	{
		return $this->belongsTo(StudentRecord::class, 'student_id');
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
		                  ->whereBetweenColumns(DB::raw(date("'Y-m-d'")), [
			                  'terms.term_start',
			                  'terms.term_end',
		                  ])
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

	public function getIntegrationServices(?IntegratorServiceTypes $type = null): Collection
	{
		if (!$type)
		{
			return $this->connectedServices;
		}

		return $this->connectedServices()
		            ->where('service_type', $type)
		            ->get();
	}

	public function removeIntegrationService(IntegrationService $service): void
	{
		if ($this->hasIntegrationService($service))
		{
			$this->connectedServices()
			     ->detach($service->id);
		}
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
		// if the prefs are empty, init them.
		if (!$this->prefs || !is_array($this->prefs) || count($this->prefs) == 0)
		{
			$this->prefs = config('lms.prefs_default', []);
		}
		// we assume the key is in dotted notation.
		$keys = explode('.', $key);
		if (!$key || count($keys) == 0)
		{
			return $default;
		}
		if (count($keys) == 1)
		{
			return $this->prefs[$key] ?? $default;
		}
		$data = $this->prefs;
		$pointer = &$data;
		foreach ($keys as $i => $k)
		{
			// is this the last one?
			if ($i == count($keys) - 1)
			{
				return $pointer[$k] ?? $default;
			}
			// is it set?
			if (!isset($pointer[$k]) || !is_array($pointer[$k]))
			{
				return $default;
			}
			$pointer = &$pointer[$k];
		}

		return $default;
	}

	public function setPreference(string $key, mixed $value): void
	{
		// if the prefs are empty, init them.
		if (!$this->prefs || !is_array($this->prefs) || count($this->prefs) == 0)
		{
			$this->prefs = config('lms.prefs_default', []);
		}
		// we assume the key is in dotted notation.
		$keys = explode('.', $key);
		if (!$key || count($keys) == 0)
		{
			return;
		}
		$data = $this->prefs;
		if (count($keys) == 1)
		{
			$data[$key] = $value;
		}
		else
		{
			$pointer = &$data;
			foreach ($keys as $i => $k)
			{
				if ($i == count($keys) - 1)
				{
					$pointer[$k] = $value;
				}
				else
				{
					if (!isset($pointer[$k]) || !is_array($pointer[$k]))
					{
						$pointer[$k] = [];
					}
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
		$query->whereHas('schoolRoles', function (Builder $query)
		{
			$query->where('name', SchoolRoles::$FACULTY);
		});
	}

	#[Scope]
	protected function students(Builder $query): void
	{
		$query->whereHas('schoolRoles', function (Builder $query)
		{
			$query->where('name', SchoolRoles::$STUDENT);
		});
	}

	#[Scope]
	protected function staff(Builder $query): void
	{
		$query->whereHas('schoolRoles', function (Builder $query)
		{
			$query->where('name', SchoolRoles::$STAFF);
		});
	}

	#[Scope]
	protected function childParents(Builder $query): void
	{
		$query->whereHas('schoolRoles', function (Builder $query)
		{
			$query->where('name', SchoolRoles::$PARENT);
		});
	}

	#[Scope]
	protected function coaches(Builder $query): void
	{
		$query->whereHas('schoolRoles', function (Builder $query)
		{
			$query->where('name', SchoolRoles::$COACH);
		});
	}

	#[Scope]
	protected function substitutes(Builder $query): void
	{
		$query->whereHas('schoolRoles', function (Builder $query)
		{
			$query->where('name', SchoolRoles::$SUBSTITUTE);
		});
	}

	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::ProfileWork;
	}

	public function shouldBePublic(): bool
	{
		return true;
	}

	public function canAccessFile(Person $person, WorkFile $file): bool
	{
		return true;
	}

	public function subbedClassRequests(): HasMany
	{
		return $this->hasMany(SubstituteClassRequest::class, 'person_id');
	}

	public function totalSubbedInYear(?Year $year = null, ?SchoolClass $schoolClass = null): int
	{
		if (!$year)
		{
			$year = Year::currentYear();
		}
		if ($schoolClass)
			return $this->subbedClassRequests()
			            ->whereIn('session_id', $schoolClass->sessions->pluck('id')->toArray())
			            ->whereBetween('created_at', [$year->start, $year->end])
			            ->count();

		return $this->subbedClassRequests()
		            ->whereBetween('created_at', [$year->start, $year->end])
		            ->count();
	}

	public function substituteRequests(): HasMany
	{
		return $this->hasMany(SubstituteRequest::class, 'requester_id');
	}
}
