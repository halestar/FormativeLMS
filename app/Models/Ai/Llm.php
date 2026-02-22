<?php

namespace App\Models\Ai;

use App\Casts\Ai\ProviderOptions;
use App\Models\Integrations\Connections\AiConnection;
use App\Models\Integrations\IntegrationConnection;
use App\Models\Scopes\OrderByOrderScope;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(OrderByOrderScope::class)]
class Llm extends Model
{
    public $timestamps = true;
    public $incrementing = true;
    protected $table = "llms";
    protected $primaryKey = "id";
    protected $guarded = ['id'];

    protected $casts =
    [
		'hide' => 'boolean',
        'provider_options' => ProviderOptions::class,
    ];

	public function provider(): BelongsTo
	{
		return $this->belongsTo(IntegrationConnection::class, 'connection_id');
	}

	#[Scope]
	protected function available(Builder $query): void
	{
		$query->where('hide' , false);
	}

}
