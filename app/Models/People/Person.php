<?php

namespace App\Models\People;

use App\Casts\LogItem;
use App\Traits\HasLogs;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
    protected $guarded = ['id', 'global_log'];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return
            [
                'dob' => 'date: m/d/Y',
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


}
