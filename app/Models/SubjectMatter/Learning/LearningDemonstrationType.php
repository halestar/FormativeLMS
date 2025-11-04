<?php

namespace App\Models\SubjectMatter\Learning;

use Illuminate\Database\Eloquent\Model;

class LearningDemonstrationType extends Model
{
	public $timestamps = false;
	public $incrementing = true;
	protected $table = "learning_demonstration_types";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'name',
			'description',
		];
}
