<?php

namespace App\Models\People;

use App\Casts\LogItem;
use App\Models\CRUD\Ethnicity;
use App\Models\CRUD\Gender;
use App\Models\CRUD\Honors;
use App\Models\CRUD\Pronouns;
use App\Models\CRUD\Suffix;
use App\Models\CRUD\Title;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Person extends Authenticatable
{
    use HasFactory, HasLogs, SoftDeletes, HasRoles;
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
                'email_verified_at' => 'datetime',
                'password' => 'hashed',
                'created_at' => 'datetime: m/d/Y h:i A',
                'updated_at' => 'datetime: m/d/Y h:i A',
            ];
    }

    protected static string $logField = 'global_log';

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
}
