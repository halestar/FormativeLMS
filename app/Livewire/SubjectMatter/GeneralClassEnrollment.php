<?php

namespace App\Livewire\SubjectMatter;

use App\Classes\SessionSettings;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\SubjectMatter\Course;
use App\Models\SubjectMatter\SchoolClass;
use App\Models\SubjectMatter\Subject;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GeneralClassEnrollment extends Component
{
    //Campus
    public Collection $campuses;
    public Campus $selectedCampus;
    public int $campusId;

    //Year
    public Collection $years;
    public Year $selectedYear;
    public int $yearId;

    //Terms
    public Collection $terms;
    public array $termIds;

    //Subjects
    public Collection $subjects;
    public Subject $selectedSubject;
    public int $subjectId;

    //Courses
    public Collection $courses;
    public Course $selectedCourse;
    public int $courseId;

    //Student Filter
    public string $studentFilter;

    //Levels

    public Collection $levels;
    public array $levelIds;


    //Classes
    public Collection $schoolClasses;

    //Students
    public Collection $students;


    public function mount()
    {
        //we will rely only on the saved session data.
        //get campus data
        $this->campuses = Auth::user()->employeeCampuses;
        $this->selectedCampus = SessionSettings::workingCampus();
        $this->campusId = $this->selectedCampus->id;
        //year data
        $this->years = Year::all();
        $this->selectedYear = SessionSettings::workingYear();
        $this->yearId = $this->selectedYear->id;
        //terms data
        $this->terms = $this->selectedYear->campusTerms($this->selectedCampus)->get();
        $this->termIds = $this->terms->pluck('id')->toArray();
        //subjects
        $this->subjects = $this->selectedCampus->subjects;
        if(SessionSettings::has('subjects.subject_id'))
        {
            $this->subjectId = SessionSettings::get('subjects.subject_id');
            $this->selectedSubject = Subject::find($this->subjectId);
        }
        else
        {
            $this->selectedSubject = $this->subjects->first();
            $this->subjectId = $this->selectedSubject->id;
            SessionSettings::set('subjects.subject_id', $this->subjectId);
        }
        //courses
        $this->courses = $this->selectedSubject->courses;
        if(SessionSettings::has('subjects.course_id'))
        {
            $this->courseId = SessionSettings::get('subjects.course_id');
            $this->selectedCourse = Course::find($this->courseId);
        }
        else
        {
            $this->selectedCourse = $this->courses->first();
            $this->courseId = $this->selectedCourse->id;
            SessionSettings::set('subjects.course_id', $this->courseId);
        }
        //student filter
        $this->studentFilter = "all";
        //levels
        $this->levels = $this->selectedCampus->levels;
        $this->levelIds = [$this->levels->first()->id];
        //classes
        $this->schoolClasses = $this->selectedCourse->schoolClasses($this->selectedYear)->get();
        //students
        $this->refreshStudents();
    }

    public function updateCampus(): void
    {
        $this->selectedCampus = Campus::find($this->campusId);
        SessionSettings::workingCampus($this->selectedCampus);
        $this->subjects = $this->selectedCampus->subjects;
        $this->subjectId = $this->subjects->first()->id;
        //levels
        $this->levels = $this->selectedCampus->levels;
        $this->levelIds = [$this->levels->first()->id];
        $this->updateYear();
    }

    public function updateYear(): void
    {
        $this->selectedYear = Year::find($this->yearId);
        $this->terms = $this->selectedYear->campusTerms($this->selectedCampus)->get();
        $this->termIds = $this->terms->pluck('id')->toArray();
        SessionSettings::workingYear($this->selectedYear);
        $this->updateSubject();
    }

    public function updateSubject(): void
    {
        $this->selectedSubject = Subject::find($this->subjectId);
        SessionSettings::set('subjects.subject_id', $this->subjectId);
        $this->courses = $this->selectedSubject->courses;
        $this->courseId = $this->courses->first()->id;
        $this->updateCourse();
    }

    public function updateCourse(): void
    {
        $this->selectedCourse = Course::find($this->courseId);
        SessionSettings::set('subjects.course_id', $this->courseId);
        //students
        $this->refreshStudents();
    }

    public function refreshStudents(): void
    {
        //classes
        $this->schoolClasses = $this->selectedCourse->schoolClasses($this->selectedYear)->get();
        $this->students = new Collection();
        if($this->studentFilter == "all")
        {
            $this->students = $this->selectedCampus
                ->students($this->selectedYear)
                ->whereIn('level_id', $this->levelIds)
                ->get();
        }
        elseif($this->studentFilter == "enrolled")
        {
            $enrolled = DB::table('class_sessions_students')->select('student_id')
                ->join('class_sessions', 'class_sessions_students.session_id', '=', 'class_sessions.id')
                ->join('school_classes', 'class_sessions.class_id', '=', 'school_classes.id')
                ->where('school_classes.course_id', $this->courseId)
                ->whereIn('class_sessions.term_id', $this->termIds);
            $this->students = $this->selectedCampus
                ->students($this->selectedYear)
                ->whereIn('level_id', $this->levelIds)
                ->whereIn('student_records.id', $enrolled)
                ->get();
        }
        elseif($this->studentFilter == "unenrolled")
        {
            $enrolled = DB::table('class_sessions_students')->select('student_id')
                ->join('class_sessions', 'class_sessions_students.session_id', '=', 'class_sessions.id')
                ->join('school_classes', 'class_sessions.class_id', '=', 'school_classes.id')
                ->where('school_classes.course_id', $this->courseId)
                ->whereIn('class_sessions.term_id', $this->termIds);
            $this->students = $this->selectedCampus
                ->students($this->selectedYear)
                ->whereIn('level_id', $this->levelIds)
                ->whereNotIn('student_records.id', $enrolled)
                ->get();
        }
        $this->students = $this->students->sortBy('person.last');
    }

    public function enrollStudents(SchoolClass $schoolClass, array $studentIds): void
    {
        foreach($schoolClass->sessions()->whereIn('term_id', $this->termIds)->get() as $session)
            $session->students()->attach($studentIds);
        $this->refreshStudents();
    }

    public function unenrollStudents(SchoolClass $schoolClass, array $studentIds): void
    {
        foreach($schoolClass->sessions()->whereIn('term_id', $this->termIds)->get() as $session)
            $session->students()->detach($studentIds);
        $this->refreshStudents();
    }

    public function render()
    {
        return view('livewire.subject-matter.general-class-enrollment');
    }
}
