<?php

namespace App\Models\People;

use App\Casts\LogItem;
use App\Classes\PreferenceManager;
use App\Classes\RoleField;
use App\Classes\Settings\SchoolSettings;
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
use App\Models\Utilities\SchoolRoles;
use App\Notifications\NewClassMessageNotification;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Lab404\Impersonate\Models\Impersonate;


class Person extends Authenticatable implements HasSchoolRoles
{
	use HasFactory, HasLogs, SoftDeletes, HasSchoolRolesTrait, Phoneable, Addressable, Notifiable, HasFullTextSearch, Campuseable, Impersonate;
	
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
		];
	protected $hidden = [
		'remember_token',
	];
	
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
	
	/**********
	 * Mutators/Accessors
	 */
	
	public function name(): Attribute
	{
		return Attribute::make
		(
			get: function(mixed $value, array $attributes)
			{
				$settings = app(SchoolSettings::class);
				if($this->isStudent())
					$name = $settings->studentName->applyName($this);
				elseif($this->isEmployee())
					$name = $settings->employeeName->applyName($this);
				elseif($this->isParent())
					$name = $settings->parentName->applyName($this);
				else
					$name = $this->first . " " . $this->last;
				return $name;
			}
		);
	}
	
	public function isStudent(): bool
	{
		return $this->hasRole(SchoolRoles::$STUDENT);
	}
	
	/**********
	 * Boolean Functions
	 */
	
	public function isEmployee(): bool
	{
		return $this->hasRole(SchoolRoles::$EMPLOYEE);
	}
	
	public function isParent(): bool
	{
		return $this->hasRole(SchoolRoles::$PARENT);
	}
	
	public function systemEmail(): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) => $attributes['email'],
		);
	}
	
	public function preferredFirst(): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) => Auth::user()
			                                                ->canViewField('preferred_first',
				                                                $this) ? $attributes['nick'] ?? $attributes['first'] : null,
		);
	}
	
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
	
	/**
	 * FIELD PERMISSIONS
	 */
	
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
	
	public function dob(): Attribute
	{
		return Attribute::make
		(
			get: fn(mixed $value, array $attributes) => Auth::user()
			                                                ->canViewField('dob', $this) ? Carbon::parse($value) : null,
		);
	}
	
	public function portraitUrl(): Attribute
	{
		return Attribute::make
		(
			get: function(mixed $value, array $attributes)
			{
				if(Auth::user()
				       ->canViewField('portrait', $this) && $attributes['portrait_url'])
					return $attributes['portrait_url'];
				return self::UKN_IMG;
			},
			set: function(null|UploadedFile|string $value, array $attributes)
			{
				$portraitDisk = config('lms.profile_pics_disk');
				if(!$value)
				{
					//we should probably try to remove the image
					Storage::disk($portraitDisk)
					       ->delete(str_replace(Storage::disk($portraitDisk)
					                                   ->url(''), '', $attributes['portrait_url']));
					Storage::disk($portraitDisk)
					       ->delete(str_replace(Storage::disk($portraitDisk)
					                                   ->url(''), '', $attributes['thumbnail_url']));
					return ['portrait_url' => null, 'thumbnail_url' => null];
				}
				elseif(is_string($value))
				{
					return ['portrait_url' => $value, 'thumbnail_url' => null];
				}
				$attr = [];
				$portraitPath = $value->store('', $portraitDisk);
				$attr['portrait_url'] = Storage::disk($portraitDisk)
				                               ->url($portraitPath);
				$manager = new ImageManager(new Driver());
				$thmb = $manager->read(Storage::disk($portraitDisk)
				                              ->get($portraitPath));
				if($thmb)
					$thmb->scaleDown(height: config('lms.thumb_max_height'));
				$thmbPath = config('lms.profile_thumbs_path') . "/" . pathinfo($portraitPath,
						PATHINFO_FILENAME) . ".png";
				$path = Storage::disk($portraitDisk)
				               ->put($thmbPath, $thmb->toPng());
				$attr['thumbnail_url'] = Storage::disk($portraitDisk)
				                                ->url($path);
				return $attr;
			}
		);
	}
	
	public function thumbnailUrl(): Attribute
	{
		return Attribute::make
		(
			get: function(mixed $value, array $attributes)
			{
				if(Auth::user()
				       ->canViewField('portrait', $this) && $attributes['thumbnail_url'])
					return $attributes['thumbnail_url'];
				return self::UKN_IMG;
			}
		);
	}
	
	public function prefs(): Attribute
	{
		return Attribute::make
		(
			get: function(mixed $value, array $attributes)
			{
				return new PreferenceManager($this, $value);
			},
			set: function(PreferenceManager $value, array $attributes)
			{
				return json_encode($value->getData());
			}
		);
	}
	
	public function schoolId(): Attribute
	{
		return Attribute::make
		(
			get: function(mixed $value, array $attributes)
			{
				return str_pad($value, 10, '0', STR_PAD_LEFT);
			},
		);
	}
	
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
	
	public function isTeacher(): bool
	{
		return $this->hasRole(SchoolRoles::$FACULTY);
	}
	
	public function hasPortrait(): bool
	{
		return ($this->attributes['portrait_url'] != null && $this->attributes['portrait_url'] != '');
	}
	
	public function hasChildren(): bool
	{
		return $this->relationships()
		            ->where('relationship_id', Relationship::CHILD)
		            ->count() > 0;
	}
	
	/**********
	 * Relationships
	 */
	
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
	
	public function isParentOfPerson(Person $target): bool
	{
		return $target->relationships()
		              ->where('to_person_id', $this->id)
		              ->where('relationship_id', Relationship::CHILD)
		              ->count() > 0;
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
	
	/**
	 * Addresses
	 */
	public function primaryAddress(): Address
	{
		return $this->addresses()
		            ->wherePivot('primary', true)
		            ->first();
	}
	
	/**
	 * Phones
	 */
	public function primaryPhone(): Phone
	{
		return $this->phones()
		            ->wherePivot('primary', true)
		            ->first();
	}
	
	/**
	 * Student Functions
	 */
	public function student(): ?StudentRecord
	{
		$year = Year::currentYear();
		return $this->studentRecords()
		            ->where('year_id', $year->id)
		            ->whereNull('end_date')
		            ->first();
	}
	
	public function studentRecords(): HasMany
	{
		return $this->hasMany(StudentRecord::class, 'person_id');
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
	
	public function parents(): BelongsToMany
	{
		return $this->relationships()
		            ->wherePivot('relationship_id', Relationship::CHILD);
	}
	
	/**
	 * Parent Functions
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
	
	public function allChildren(): BelongsToMany
	{
		return $this->relationships()
		            ->wherePivot('relationship_id', Relationship::CHILD);
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
	
	/**
	 * Teacher functions
	 */
	public function currentClassSessions(): BelongsToMany
	{
		return $this->belongsToMany(ClassSession::class, 'class_sessions_teachers', 'person_id', 'session_id')
		            ->join('terms', 'terms.id', '=', 'class_sessions.term_id')
		            ->whereBetweenColumns(DB::raw(date("'Y-m-d'")), ['terms.term_start', 'terms.term_end']);
	}
	
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
	
	public function classesTaught(): BelongsToMany
	{
		return $this->belongsToMany(ClassSession::class, 'class_sessions_teachers', 'person_id', 'session_id');
	}
	
	public function learningDemonstrationTemplates(): HasMany
	{
		return $this->hasMany(LearningDemonstrationTemplate::class, 'person_id');
	}
	
	/**
	 * Notifications
	 */
	public function alertNotifications()
	{
		return $this->notifications()
		            ->unread()
		            ->whereNot('type', NewClassMessageNotification::class);
	}
	
	public function classMessageNotifications()
	{
		return $this->notifications()
		            ->unread()
		            ->where('type', NewClassMessageNotification::class);
	}
	
	public function getIntegrationServices(IntegratorServiceTypes $type = null): Collection
	{
		if(!$type)
			return $this->connectedServices;
		return $this->connectedServices()
		            ->where('service_type', $type)
		            ->get();
	}
	
	public function connectedServices(): BelongsToMany
	{
		return $this->belongsToMany(IntegrationService::class, 'integration_connections', 'person_id', 'service_id')
		            ->withPivot('id', 'data', 'enabled', 'className')
		            ->as('lms_service_connection')
		            ->using(IntegrationConnection::class);
	}
	
	public function removeIntegrationService(IntegrationService $service)
	{
		if($this->hasIntegrationService($service))
			$this->connectedServices()
			     ->detach($service->id);
	}
	
	/*****
	 * Integration Functions
	 */
	public function hasIntegrationService(IntegrationService $service)
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
	
	/**
	 * Admin Functions
	 */
	
	public function aiAccess(): null|Collection
	{
		$connections = new Collection();
		//AI services
		$aiServices = IntegrationService::where('service_type', IntegratorServiceTypes::AI)
		                                ->get();
		//check if the user has access to the system's AI
		if($this->can('system.ai'))
		{
			//they do, so cycle through each, connecting to it
			foreach($aiServices as $service)
			{
				$conn = $service->connectToSystem();
				//if we establish a connection, add it to the list
				if($conn)
					$connections->push($conn);
			}
		}
		//next, do we have any personal connections to the AI system?
		foreach($aiServices as $service)
		{
			$conn = $service->connect($this);
			//if we establish a connection, add it to the list
			if($conn)
				$connections->push($conn);
		}
		return $aiServices->count() > 0 ? $connections : null;
	}
	
	public function __toString()
	{
		return $this->name;
	}
	
	public function canImpersonate()
	{
		return $this->can('people.impersonate');
	}
	
	public function canBeImpersonated()
	{
		return !$this->hasRole(SchoolRoles::$ADMIN) && ($this->id != auth()->user()->id);
	}
	
	protected function casts(): array
	{
		return
			[
				'dob' => 'date: m/d/y',
				'global_log' => LogItem::class,
				'prefs' => 'array',
				'created_at' => 'datetime: m/d/Y h:i A',
				'updated_at' => 'datetime: m/d/Y h:i A',
			];
	}
	
	/**********
	 * Scopes
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
