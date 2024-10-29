<?php

namespace App\Models\People;

use App\Casts\LogItem;
use App\Models\CRUD\Ethnicity;
use App\Models\CRUD\Gender;
use App\Models\CRUD\Honors;
use App\Models\CRUD\Pronouns;
use App\Models\CRUD\Suffix;
use App\Models\CRUD\Title;
use App\Models\People\ViewPolicies\ViewableField;
use App\Models\People\ViewPolicies\ViewPolicy;
use App\Models\Utilities\SchoolRoles;
use App\Traits\HasLogs;
use App\Traits\HasViewableFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use Spatie\Permission\Traits\HasRoles;

class Person extends Authenticatable
{
    use HasFactory, HasLogs, SoftDeletes, HasRoles, HasViewableFields;
    protected $with = ['roles'];
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
            'ethnicity_id',
            'title_id',
            'suffix_id',
            'honors_id',
            'gender_id',
            'pronoun_id',
            'occupation',
            'job_title',
            'work_company',
            'salutation',
            'family_salutation'
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

    public function portraitUrl(): Attribute
    {
        return Attribute::make
        (
            get: function(mixed $value, array $attributes)
            {
                if($attributes['portrait_url'])
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
                if($attributes['thumbnail_url'])
                    return $attributes['thumbnail_url'];
                return 'data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg>';
            }
        );
    }

    /**********
     * Relationships
     */
    public function ethnicity(): BelongsTo
    {
        return $this->belongsTo(Ethnicity::class, 'ethnicity_id');
    }

    public function title(): BelongsTo
    {
        return $this->belongsTo(Title::class, 'title_id');
    }

    public function suffix(): BelongsTo
    {
        return $this->belongsTo(Suffix::class, 'suffix_id');
    }

    public function honors(): BelongsTo
    {
        return $this->belongsTo(Honors::class, 'honors_id');
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function pronouns(): BelongsTo
    {
        return $this->belongsTo(Pronouns::class, 'pronoun_id');
    }

    public function viewPolicies(): Collection
    {
        return ViewPolicy::select('view_policies.*')
            ->with(['fields'])
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'view_policies.role_id')
            ->where('model_has_roles.model_type', '=', Person::class)
            ->where('model_id', $this->id)
            ->get();
        /*return Cache::rememberForever('view-policies-' . $this->id, function()
        {
            ViewPolicy::select('view_policies.*')
                ->with(['fields'])
                ->join('model_has_roles', 'model_has_roles.role_id', '=', 'view_policies.role_id')
                ->where('model_has_roles.model_type', '=', Person::class)
                ->where('model_id', $this->id)
                ->get();
        });*/

    }

    public function addresses(): BelongsToMany
    {
        return $this->belongsToMany(Address::class, 'people_addresses')
            ->using(PersonalAddress::class)
            ->as('personal')
            ->withPivot(
                [
                    'primary', 'work', 'seasonal',
                    'season_start','season_end',
                ])
            ->orderBy('people_addresses.primary', 'desc')
            ->orderBy('people_addresses.work', 'desc');
    }

    public function phones(): BelongsToMany
    {
        return $this->belongsToMany(Phone::class, 'people_phones')
            ->using(PersonalPhone::class)
            ->as('personal')
            ->withPivot(
                [
                    'primary', 'work',
                ])
            ->orderBy('people_phones.primary', 'desc')
            ->orderBy('people_phones.work', 'desc');
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

    public function canViewField(ViewableField|string $testingField, Person $target): bool
    {
        if($this->can('people.view'))
            return true;
        if(is_string($testingField))
            $testingField = ViewableField::where('name', $testingField)->first();
        if(!$testingField)
            return false;
        //find the fields specifically in relation to the policicies that
        //belong to this person.
        $policyFields = [];
        foreach($target->viewPolicies() as $policy)
        {
            if($policy->fields->where('id', '=', $testingField->id)->count() > 0)
                $policyFields[] = $policy->fields->where('id', '=', $testingField->id)->first();
        }
        //when deciding the priorities of the policies, we always go through a allow, deny method. Meaning that if we find
        //any policy where we're  allowed to see it, then it is allowed. Default is always to deny though.
        if($target->id == $this->id)
        {
            //self: There is only one field that we care in here, and that is the view or not.
            foreach($policyFields as $field)
            {
                if ($field->permissions->self_viewable)
                    return true;
            }
            return false;
        }
        elseif($this->isEmployee())
        {
            //employee: an enforced field is always prioritized, with it showing as a priority, else if not enforced, we go to settings.
            $isEnforced = false;
            foreach($policyFields as $field)
            {
                if($field->permissions->employee_enforce)
                {
                    if($field->permissions->employee_viewable)
                        return true;
                    $isEnforced = true;
                }
            }
            if($isEnforced)
                return false;
            return ($target->viewingPreferences())[$testingField->id]?? false;

        }
        elseif($this->isStudent())
        {
            //student: an enforced field is always prioritized, with it showing as a priority, else if not enforced, we go to settings.
            $isEnforced = false;
            foreach($policyFields as $field)
            {
                if($field->permissions->student_enforce)
                {
                    if($field->permissions->student_viewable)
                        return true;
                    $isEnforced = true;
                }
            }
            if($isEnforced)
                return false;
            return ($target->viewingPreferences())[$testingField->id]?? false;
        }
        elseif($this->isParent())
        {
            //parent: an enforced field is always prioritized, with it showing as a priority, else if not enforced, we go to settings.
            $isEnforced = false;
            foreach($policyFields as $field)
            {
                if($field->permissions->parent_enforce)
                {
                    if($field->permissions->parent_viewable)
                        return true;
                    $isEnforced = true;
                }
            }
            if($isEnforced)
                return false;
            return ($target->viewingPreferences())[$testingField->id]?? false;
        }
        return false;
    }

    public function canEditField(ViewableField $testingField): bool
    {
        //if we have the global edit, then we can just return true.
        if($this->can('people.edit'))
            return true;
        //find the fields specifically in relation to the policicies that
        //belong to this person.
        $policyFields = [];
        foreach($this->viewPolicies() as $policy)
        {
            if($policy->fields->where('id', '=', $testingField->id)->count() > 0)
                $policyFields[] = $policy->fields->where('id', '=', $testingField->id)->first();
        }
        //if a policy exists that we can edit, then we return true
        foreach($policyFields as $field)
        {
            if ($field->permissions->editable)
                return true;
        }
        return false;
    }

    /**
     * Viewable Fields
     */
    public function unenforcedFields(): Collection
    {
        return ViewableField::select('viewable_fields.*')
            ->join('view_policies_fields', 'view_policies_fields.field_id', '=', 'viewable_fields.id')
            ->join('view_policies', 'view_policies.id', '=', 'view_policies_fields.policy_id')
            ->join('model_has_roles', 'model_has_roles.role_id', '=', 'view_policies.role_id')
            ->where('model_has_roles.model_type', '=', Person::class)
            ->where('model_id', $this->id)
            ->where(function($query)
            {
                $query->where('view_policies_fields.employee_enforce', false)
                    ->orWhere('view_policies_fields.parent_enforce', false)
                    ->orWhere('view_policies_fields.student_enforce', false);
            })
            ->groupBy('viewable_fields.id')
            ->orderBy('group_id')
            ->orderBy('order')
            ->get();
    }

    public function viewingPreferences(): array
    {
        if(!isset($this->prefs['viewing']))
        {
            //create the base one based on the all the policies.
            $viewingPrefs = [];
            foreach($this->unenforcedFields() as $field)
                $viewingPrefs[$field->id] = false;
            $prefs = $this->prefs;
            $prefs['viewing'] = $viewingPrefs;
            $this->prefs = $prefs;
            $this->save();
        }
        return $this->prefs['viewing'];
    }

    public function updateViewingPreferences(array $viewingPrefs): void
    {
        $prefs = $this->prefs;
        $prefs['viewing'] = $viewingPrefs;
        $this->prefs = $prefs;
        $this->save();
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

}
