<?php

namespace App\Models\Utilities;

use App\Classes\Settings\EmailSetting;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Models\People\Person;
use App\Traits\HasWorkFiles;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Blade;

class SchoolMessage extends Model implements Fileable, Arrayable, Jsonable
{
    use HasWorkFiles;
    public $timestamps = true;
    public $incrementing = true;
    protected $table = "school_messages";
    protected $primaryKey = "id";
    protected $fillable =
        [
            'force_subscribe',
            'name',
            'description',
            'send_email',
            'send_sms',
            'send_push',
            'subject',
            'body',
            'short_subject',
            'short_body',
            'notification_class',
            'enabled',
        ];

    protected function casts(): array
    {
        return
        [
            'system' => 'boolean',
            'subscribable' => 'boolean',
            'force_subscribe' => 'boolean',
            'send_email' => 'boolean',
            'send_sms' => 'boolean',
            'send_push' => 'boolean',
            'enabled' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        //clean up on save.
        static::saved(function(SchoolMessage $message)
        {
            $message->cleanup();
        });
    }

    public function cleanup()
    {
        //cleans up the files in the conmtent. Essentially it pulls in all the files
        //references in the content and syncs them to this email.
        $fileRefs = [];
        //attempting to match src="https://fablms.app/settings/work-files/(file uuid)"
        $pattern = '@src="' . config('app.url') .
            '/settings/work-files/([0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12})"@';
        if(preg_match($pattern, $this->body, $fileRefs))
        {
            array_shift($fileRefs);
            //we need to iterate through each model and delete each one individually, since that's the way to
            //trigger the file delete for each model.
            foreach(WorkFile::invisible()
                        ->whereNotIn('id', $fileRefs)
                        ->get() as $file)
                $file->delete();
        }
        else
        {
            //there are no file refs in the content, so we delete all hidden files
            foreach(WorkFile::invisible()
                        ->get() as $file)
                $file->delete();
        }
    }

    #[Scope]
    protected function enabled(Builder $query): void
    {
        $query->where('enable', true);
    }

    #[Scope]
    protected function forced(Builder $query): void
    {
        $query->where('force_subscribe', false);
    }

    #[Scope]
    protected function system(Builder $query): void
    {
        $query->where('system', true);
    }

    #[Scope]
    protected function subscribable(Builder $query): void
    {
        $query->where('force_subscribe', false)->where('subscribable', true);
    }

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(Person::class, 'school_messages_subscriptions', 'message_id', 'person_id');
    }

    public function isSubscribed(Person $person): bool
    {
        return $this->subscribers()->wherePivot('person_id', $person->id)->exists();
    }

    public function getWorkStorageKey(): WorkStoragesInstances
    {
        return WorkStoragesInstances::EmailWork;
    }

    public function shouldBePublic(): bool
    {
        return true;
    }

    public function toRenderedArray($tokens)
    {
        $arr = $this->toArray();
        $arr['body'] = Blade::render($arr['body'], $tokens);
        $arr['short_body'] = Blade::render($arr['short_body'], $tokens);
        return $arr;
    }
}
