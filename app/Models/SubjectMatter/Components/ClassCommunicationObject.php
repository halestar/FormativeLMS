<?php

namespace App\Models\SubjectMatter\Components;

use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\People\Person;
use App\Models\SubjectMatter\ClassSession;
use App\Models\Utilities\WorkFile;
use App\Traits\UsesJsonValue;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasWorkFiles;

class ClassCommunicationObject extends Model implements Fileable
{
    use HasUuids, UsesJsonValue, HasWorkFiles;
    protected $table = 'class_communication_objects';
    protected $fillable =
	    [
			'value',
		    'className',
		    'posted_by',
	    ];
    public $timestamps = true;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = "id";

    /*****************************************************************
     * OVERRIDES
     */

    public function newFromBuilder($attributes = [], $connection = null)
    {
        if($attributes instanceof \stdClass)
            $attributes = json_decode(json_encode($attributes), true);
        if($attributes['className'] == static::class)
            return parent::newFromBuilder($attributes, $connection);
        return (new $attributes['className'])->newFromBuilder($attributes, $connection);
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'session_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'posted_by');
    }

	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return WorkStoragesInstances::ClassWork;
	}

	public function shouldBePublic(): bool
	{
		return true;
	}


	public function canAccessFile(Person $person, WorkFile $file): bool
	{
		return true;
	}
}
