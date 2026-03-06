<?php

namespace App\Models\Substitutes;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SubstituteToken extends Model
{
    protected $table = 'substitute_tokens';

    protected $primaryKey = 'token';

    public $timestamps = true;

    public $incrementing = false;

    public $keyType = 'string';

    public ?string $plainTextToken = null;

    public $guarded = ['token'];

    protected function casts(): array
    {
        return
            [
                'expires_at' => 'datetime:Y-m-d h:i A',
            ];
    }

    protected static function booted(): void
    {
        static::creating(function (SubstituteToken $subToken) {
            do {
                $token = Str::random(8);
            } while (SubstituteToken::where('token', hash('sha256', $token))->exists());
            $subToken->plainTextToken = $token;
            $subToken->token = hash('sha256', $token);
        });
    }

    public function substitute(): BelongsTo
    {
        return $this->belongsTo(Substitute::class, 'substitute_id');
    }

    public function subRequest(): BelongsTo
    {
        return $this->belongsTo(SubstituteRequest::class, 'request_id');
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->whereNowOrFuture('expires_at');
    }

    public function campusRequests(): BelongsToMany
    {
        return $this->belongsToMany(SubstituteCampusRequest::class, 'substitute_tokens_campuses', 'token', 'campus_request_id');
    }

    public function classRequests(): Collection
    {
        return SubstituteClassRequest::select('substitute_class_requests.*')
            ->join('sub_token_campuses', 'sub_token_campuses.campus_request_id', '=', 'substitute_class_requests.campus_request_id')
            ->where('token', $this->token)
            ->get();
    }

    public static function byToken(string $token): ?SubstituteToken
    {
        return SubstituteToken::query()->active()->where('token', hash('sha256', $token))->first();
    }

    public function regenerateToken(): void
    {
        do {
            $token = Str::random(8);
        } while (SubstituteToken::where('token', hash('sha256', $token))->exists());
        $this->plainTextToken = $token;
        $this->token = hash('sha256', $token);
        $this->created_at = now();
        $this->updated_at = now();
        $this->save();
    }
}
