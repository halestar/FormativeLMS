<?php

namespace App\Models\People;

use App\Casts\LogItem;
use App\Classes\RoleField;
use App\Models\CRUD\Relationship;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\Utilities\SchoolRoles;
use App\Traits\Addressable;
use App\Traits\HasLogs;
use App\Traits\HasViewableFields;
use App\Traits\Phoneable;
use Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Person extends Authenticatable
{
    use HasFactory, HasLogs, SoftDeletes, HasRoles, Phoneable, Addressable;
    protected $with = ['schoolRoles'];
    public $timestamps = true;
    protected $table = "people";
    protected $primaryKey = "id";
    public $incrementing = true;
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
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return
            [
                'dob' => 'date: m/d/y',
                'global_log' => LogItem::class,
                'prefs' => 'array',
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'created_at' => 'datetime: m/d/Y h:i A',
                'updated_at' => 'datetime: m/d/Y h:i A',
            ];
    }

    protected static string $logField = 'global_log';

    protected static function booted(): void
    {
        static::addGlobalScope('name_order', function (Builder $builder)
        {
            $builder->orderBy('last')->orderBy('first');
        });
    }

    /**********
     * Mutators/Accessors
     */
    public function name(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) =>
                ($attributes['nick']?? $attributes['first']) . " " . $attributes['last']
        );
    }

    public function first(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) => Auth::user()->canViewField('first', $this)? $value: null,
        );
    }

    public function middle(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) => Auth::user()->canViewField('middle', $this)? $value: null,
        );
    }

    public function last(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) => Auth::user()->canViewField('last', $this)? $value: null,
        );
    }

    public function email(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) => Auth::user()->canViewField('email', $this)? $value: null,
        );
    }


    public function nick(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) => Auth::user()->canViewField('nick', $this)? $value: null,
        );
    }


    public function dob(): Attribute
    {
        return Attribute::make
        (
            get: fn(mixed $value, array $attributes) => Auth::user()->canViewField('dob', $this)? Carbon::parse($value): null,
        );
    }

    public function portraitUrl(): Attribute
    {
        return Attribute::make
        (
            get: function(mixed $value, array $attributes)
            {
                if(Auth::user()->canViewField('portrait', $this) && $attributes['portrait_url'])
                    return $attributes['portrait_url'];
                return 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>';
            },
            set: function(null|UploadedFile|string $value, array $attributes)
            {
                $portraitDisk = config('lms.profile_pics_disk');
                if(!$value)
                {
                    //we should probably try to remove the image
                    Storage::disk($portraitDisk)
                        ->delete(str_replace(Storage::disk($portraitDisk)->url(''), '', $attributes['portrait_url']));
                    Storage::disk($portraitDisk)
                        ->delete(str_replace(Storage::disk($portraitDisk)->url(''), '', $attributes['thumbnail_url']));
                    return ['portrait_url' => null, 'thumbnail_url' => null];
                }
                elseif(is_string($value))
                {
                    return ['portrait_url' => $value, 'thumbnail_url' => null];
                }
                $attr = [];
                $portraitPath = $value->store('', $portraitDisk);
                $attr['portrait_url'] = Storage::disk($portraitDisk)->url($portraitPath);
                $manager = new ImageManager(new Driver());
                $thmb = $manager->read(Storage::disk($portraitDisk)->get($portraitPath));
                if($thmb)
                    $thmb->scaleDown(height: config('lms.thumb_max_height'));
                $thmbPath = config('lms.profile_thumbs_path') . "/" . pathinfo($portraitPath, PATHINFO_FILENAME) . ".png";
                $path = Storage::disk($portraitDisk)->put($thmbPath, $thmb->toPng());
                $attr['thumbnail_url'] = Storage::disk($portraitDisk)->url($path);
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
                if(Auth::user()->canViewField('portrait', $this) && $attributes['thumbnail_url'])
                    return $attributes['thumbnail_url'];
                return 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>';
            }
        );
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

    public function employeeCampuses(): BelongsToMany
    {
        return $this->belongsToMany(Campus::class, "employee_campuses", "person_id", "campus_id");
    }

    public function schoolRoles(): BelongsToMany
    {
        return $this->roles()->withPivot('field_values');
    }

    public function studentRecords(): HasMany
    {
        return $this->hasMany(StudentRecord::class, 'person_id');
    }


    /**********
     * Boolean Functions
     */

    public function isEmployee(): bool
    {
        return $this->hasRole(SchoolRoles::$EMPLOYEE);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(SchoolRoles::$STUDENT);
    }

    public function isParent(): bool
    {
        return $this->hasRole(SchoolRoles::$PARENT);
    }

    public function hasPortrait(): bool
    {
        return ($this->attributes['portrait_url'] != null && $this->attributes['portrait_url'] != '');
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

    public function editableFields(): Collection
    {
        return FieldPermission::where('editable', true)->get();
    }

    public function selfViewableFields(): Collection
    {
        return FieldPermission::where('by_self', true)->get();
    }

    public function canViewField(RoleField|string $testingField, Person $target): bool
    {
        if($this->can('people.view'))
            return true;
        if($target->id == $this->id)
        {
            if ($testingField instanceof RoleField)
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
            if ($testingField instanceof RoleField)
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

    /**
     * Addresses
     */
    public function primaryAddress(): Address
    {
        return $this->addresses()->wherePivot('primary', true)->first();
    }

    /**
     * Phones
     */
    public function primaryPhone(): Phone
    {
        return $this->phones()->wherePivot('primary', true)->first();
    }

    /**
     * Student Functions
     */
    public function student(): ?StudentRecord
    {
        $year = Year::currentYear();
        return $this->studentRecords()->where('year_id', $year->id)->whereNull('end_date')->first();
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

}
