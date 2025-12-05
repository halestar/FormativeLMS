<?php

namespace App\Models\Utilities;

use App\Enums\AssessmentStrategyCalculationMethod;
use App\Enums\WorkStoragesInstances;
use App\Interfaces\Fileable;
use App\Traits\HasWorkFiles;
use App\Traits\UsesJsonValue;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TemporaryFiler extends Model implements Fileable
{
	use HasUuids, HasWorkFiles;
	protected $table = 'temporary_filers';
	protected $fillable =
		[
			'person_id',
			'storage_type',
		];
	public $timestamps = true;
	public $incrementing = false;
	protected $keyType = 'string';
	protected $primaryKey = "id";

	protected function casts(): array
	{
		return
			[
				'storage_type' => WorkStoragesInstances::class,
			];
	}

	public function getWorkStorageKey(): WorkStoragesInstances
	{
		return $this->storage_type;
	}

	public function shouldBePublic(): bool
	{
		return false;
	}

	public static function getInstance(WorkStoragesInstances $storage_type): TemporaryFiler
	{
		$filer = TemporaryFiler::where('person_id', Auth()->user()->id)
			->where('storage_type', $storage_type)
			->first();
		if(!$filer)
		{
			$filer = new TemporaryFiler();
			$filer->person_id = Auth()->user()->id;
			$filer->storage_type = $storage_type;
			$filer->save();
		}
		return $filer;
	}

	public function transferFiles(Fileable $transferTo): void
	{
		$transferTo->workFiles()->saveMany($this->workFiles);
	}

	public function empty(): void
	{
		foreach($this->workFiles as $file)
			$file->delete();
	}
}
