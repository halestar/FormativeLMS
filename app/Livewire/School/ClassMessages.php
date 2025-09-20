<?php

namespace App\Livewire\School;

use App\Models\Locations\Term;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use App\Models\SubjectMatter\ClassSession;
use App\Models\SubjectMatter\Components\ClassMessage;
use App\Models\Utilities\SchoolRoles;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ClassMessages extends Component
{
	//Owner
	public Person $self;
	//View as vars
	public bool $isFaculty = false;
	public bool $isStudent = false;
	public bool $isParent = false;
	public bool $multipleRoles = false;
	public string $viewAs;
	
	//Master set of student records for the parent
	public Collection $children;
	
	//Select Collections
	public Collection $years;
	public Collection $terms;
	public Collection $sessions;
	public Collection $students;
	
	//Select Models
	public ?Year $selectedYear = null;
	public ?Term $selectedTerm = null;
	public ?ClassSession $selectedSession = null;
	public ?StudentRecord $selectedStudent = null;
	
	//Select Indexes
	public ?int $selectedStudentId = null;
	public ?int $selectedYearId = null;
	public ?int $selectedTermId = null;
	public ?int $selectedSessionId = null;
	
	//Flags
	private bool $mounting = false;
	
	public function mount(int $classMessageId = null)
	{
		$classMessage = null;
		if($classMessageId)
			$classMessage = ClassMessage::find($classMessageId);
		$this->self = Auth::user();
		$this->isParent = $this->self->isParent() || $this->self->hasRole(SchoolRoles::$OLD_PARENT);
		$this->isStudent = $this->self->isStudent() || $this->self->hasRole(SchoolRoles::$OLD_STUDENT);
		$this->isFaculty = $this->self->isTeacher() || $this->self->hasRole(SchoolRoles::$OLD_FACULTY);
		//do we have multiple roles?
		if($this->isFaculty + $this->isStudent + $this->isParent > 1)
			$this->multipleRoles = true;
		$this->viewAs = $this->isFaculty ? 'faculty' : ($this->isStudent ? 'student' : 'parent');
		$this->mounting = true;
		$this->setViewAs($classMessage);
	}
	
	
	public function setViewAs(ClassMessage $classMessage = null)
	{
		//clear the variables
		$this->years = new Collection();
		$this->selectedYear = null;
		$this->terms = new Collection();
		$this->selectedTerm = null;
		$this->students = new Collection();
		$this->selectedStudentId = null;
		$this->children = new Collection();
		$this->sessions = new Collection();
		$this->selectedSession = null;
		
		//next, we figure out years.
		if($this->viewAs == 'faculty')
		{
			//get years taught
			$this->years = Year::select('years.*')
			                   ->join('school_classes', 'school_classes.year_id', '=', 'years.id')
			                   ->join('class_sessions', 'class_sessions.class_id', '=', 'school_classes.id')
			                   ->join('class_sessions_teachers', 'class_sessions_teachers.session_id', '=',
				                   'class_sessions.id')
			                   ->where('class_sessions_teachers.person_id', $this->self->id)
			                   ->groupBy('years.id')
			                   ->get();
			
		}
		elseif($this->viewAs == "student")
		{
			//years that the student has attended
			$this->years = Year::select('years.*')
			                   ->join('student_records', 'student_records.year_id', '=', 'years.id')
			                   ->where('student_records.person_id', $this->self->id)
			                   ->groupBy('years.id')
			                   ->get();
		}
		elseif($this->viewAs == "parent")
		{
			//first, we get their kids
			$this->children = $this->self->allStudentRecords();
			//now we go over the kids and sort the years
			$years = new Collection();
			foreach($this->children as $record)
			{
				if(!isset($years[$record->year_id]))
					$years[$record->year_id] = $record->year;
			}
			$this->years = $years->sortBy('start_date');
		}
		//if we have a class message, then we select the year based on the selected student.
		if($classMessage)
			$this->selectedYearId = $classMessage->student->year_id;
		else //selected year will be the latest year
			$this->selectedYearId = $this->years->last()->id;
		//next, we get the terms taught in the selected year (assuming we got one)
		if($this->selectedYearId)
			$this->setYear($classMessage);
	}
	
	public function setYear(ClassMessage $classMessage = null)
	{
		//clear the variables
		$this->terms = new Collection();
		$this->selectedTerm = null;
		$this->students = new Collection();
		$this->selectedStudentId = null;
		$this->sessions = new Collection();
		$this->selectedSession = null;
		//se the selectecd year id
		$this->selectedYear = $this->years->where('id', $this->selectedYearId)
		                                  ->first();
		if($this->viewAs == 'faculty' || $this->viewAs == "student")
		{
			if($this->viewAs == 'faculty')
			{
				$this->terms = Term::select('terms.*')
				                   ->join('class_sessions', 'class_sessions.term_id', '=', 'terms.id')
				                   ->join('class_sessions_teachers', 'class_sessions_teachers.session_id', '=',
					                   'class_sessions.id')
				                   ->where('class_sessions_teachers.person_id', $this->self->id)
				                   ->groupBy('terms.id')
				                   ->get();
			}
			elseif($this->viewAs == "student")
			{
				$this->terms = Term::select('terms.*')
				                   ->join('years', 'years.id', '=', 'terms.year_id')
				                   ->join('student_records', function(JoinClause $join)
				                   {
					                   $join->on('student_records.year_id', '=', 'years.id')
					                        ->on('student_records.campus_id', '=', 'terms.campus_id');
				                   })
				                   ->where('student_records.person_id', $this->self->id)
				                   ->groupBy('terms.id')
				                   ->get();
			}
			//do we have a class message?
			if($classMessage)
			{
				//in this case, we use the term of the passed session.
				$this->selectedTermId = $classMessage->session->term_id;
			}
			else
			{
				$this->selectedTermId = $this->terms->first(function(Term $value)
				{
					return $value->isCurrent();
				})->id;
				if(!$this->selectedTermId)
					$this->selectedTermId = $this->terms->first()->id;
			}
			if($this->selectedTermId)
				$this->setTerm($classMessage);
		}
		elseif($this->viewAs == "parent")
		{
			//make this students select
			$this->students = $this->children->where('year_id', $this->selectedYearId);
			//do we have a class message?
			if($classMessage)
			{
				$this->selectedStudentId = $classMessage->student_id;
			}
			else
			{
				//can we save the old selected child?
				if($this->selectedStudent)
				{
					$oldPersonId = $this->selectedStudent->person_id;
					$this->selectedStudentId = $this->students->first(function(StudentRecord $value) use ($oldPersonId)
					{
						return $value->person_id == $oldPersonId;
					})->id;
					if(!$this->selectedStudentId)
						$this->selectedStudentId = $this->students->first()->id;
				}
				else
					$this->selectedStudentId = $this->students->first()->id;
			}
			if($this->selectedStudentId)
				$this->setStudent($classMessage);
		}
	}
	
	public function setTerm(ClassMessage $classMessage = null)
	{
		$this->sessions = new Collection();
		$this->selectedSession = null;
		//first, we load the term
		$this->selectedTerm = $this->terms->where('id', $this->selectedTermId)
		                                  ->first();
		if($this->viewAs == 'faculty')
		{
			$this->sessions = $this->self->classesTaught()
			                             ->where('term_id', $this->selectedTerm->id)
			                             ->get();
			if($classMessage)
				$this->selectedSessionId = $classMessage->session_id;
			elseif($this->sessions->count() > 0)
				$this->selectedSessionId = $this->sessions->first()->id;
			if($this->selectedSessionId)
				$this->setSession($classMessage);
		}
		elseif($this->viewAs == "student" || $this->viewAs == "parent")
		{
			if($this->mounting)
			{
				//in this case, first we check if we have some defaults.
				if($classMessage)
				{
					//set the missing session (we have student), or null
					$this->selectedSessionId = $classMessage->session_id;
				}
				//and we unset mounting
				$this->mounting = false;
			} //we've already mounted the system, so we will execute a command to go to the correct chat.
			elseif($this->viewAs == "student")
				$this->dispatch('change-term', termId: $this->selectedTermId);
			else
				$this->dispatch('change-student-term', studentId: $this->selectedStudentId,
					termId: $this->selectedTermId);
		}
	}
	
	public function setSession(ClassMessage $classMessage = null)
	{
		//first, we load the session
		$this->selectedSession = ClassSession::find($this->selectedSessionId);
		if($this->mounting)
		{
			//in this case, check if we have a default
			if($classMessage)
			{
				//set the missing student (we have session), or null
				$this->selectedStudentId = $classMessage->student_id;
			}
			//and we unset mounting
			$this->mounting = false;
		}
		else //we've already mounted the system, so we will execute a command to go to the correct chat.
			$this->dispatch('change-session', sessionId: $this->selectedSessionId);
	}
	
	public function setStudent(ClassMessage $classMessage = null)
	{
		//clear the variables
		$this->terms = new Collection();
		$this->selectedTerm = null;
		$this->sessions = new Collection();
		$this->selectedSession = null;
		//set the selected student id
		$this->selectedStudent = $this->students->where('id', $this->selectedStudentId)
		                                        ->first();
		//now we make the terms
		$this->terms = Term::select('terms.*')
		                   ->join('years', 'years.id', '=', 'terms.year_id')
		                   ->join('student_records', function(JoinClause $join)
		                   {
			                   $join->on('student_records.year_id', '=', 'years.id')
			                        ->on('student_records.campus_id', '=', 'terms.campus_id');
		                   })
		                   ->where('student_records.id', $this->selectedStudentId)
		                   ->groupBy('terms.id')
		                   ->get();
		//do we have a message?
		if($classMessage)
			$this->selectedTermId = $classMessage->session->term_id;
		else
		{
			$this->selectedTermId = $this->terms->first(function(Term $value)
			{
				return $value->isCurrent();
			})->id;
			if(!$this->selectedTermId)
				$this->selectedTermId = $this->terms->first()->id;
		}
		if($this->selectedTermId)
			$this->setTerm($classMessage);
	}
	
	public function render()
	{
		return view('livewire.school.class-messages');
	}
}
