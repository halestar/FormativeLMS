<?php

namespace App\Models\SubjectMatter\Components;

use App\Classes\Settings\SchoolSettings;
use App\Enums\ClassViewer;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\Scopes\OrderByLatest;
use App\Models\SubjectMatter\ClassSession;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(OrderByLatest::class)]
class ClassMessage extends Model
{
	use HasFactory;
	public $timestamps = true;
	public $incrementing = true;
	protected $table = "class_messages";
	protected $primaryKey = "id";
	protected $fillable =
		[
			'session_id',
			'student_id',
			'person_id',
			'message',
			'from_type'
		];
	
	public static function numUnreadMessages(ClassSession $session, StudentRecord $student, Person $unreadBy): int
	{
        $settings = app(SchoolSettings::class);
        if($settings->year_messages == SchoolSettings::YEAR)
            $query = ClassMessage::whereIn('session_id', $session->schoolClass->sessions->pluck('id'));
        else
            $query = ClassMessage::where('session_id', $session->id);
		return $query->where('student_id', $student->id)
		             ->where('created_at', '>', $unreadBy->getPreference('session.' . $session->id .
			                   '.messages.student.' . $student->id . '.last_read',
			                   $session->term->term_start->format('Y-m-d H:i:s')))
		             ->count();
	}

	
	public static function latestMessage(ClassSession $session, StudentRecord $student): ?ClassMessage
	{
		return ClassMessage::where('session_id', $session->id)
		                   ->where('student_id', $student->id)
		                   ->orderBy('created_at', 'desc')
		                   ->first();
	}
	
	public function session(): BelongsTo
	{
		return $this->belongsTo(ClassSession::class, 'session_id');
	}
	
	public function student(): BelongsTo
	{
		return $this->belongsTo(StudentRecord::class, 'student_id');
	}
	
	public function postedBy(): BelongsTo
	{
		return $this->belongsTo(Person::class, 'person_id');
	}
	
	protected function casts(): array
	{
		return
			[
			    'from_type' => ClassViewer::class,
			];
	}
}
