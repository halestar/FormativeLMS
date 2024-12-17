<?php

namespace App\Livewire\People;

use App\Models\CRUD\DismissalReason;
use App\Models\CRUD\Level;
use App\Models\Locations\Campus;
use App\Models\Locations\Year;
use App\Models\People\Person;
use App\Models\People\StudentRecord;
use Illuminate\Support\Collection;
use Livewire\Component;

class StudentRecordManager extends Component
{
    public Person $person;
    public bool $editing = false;
    public Collection $studentRecords;

    public function refreshRecords()
    {
        $this->studentRecords = $this->person->studentRecords
            ->sortByDesc(function(StudentRecord $record, int $key)
            {
                return $record->year->year_start;
            });
    }

    public function mount(Person $person)
    {
        $this->person = $person;
        $this->refreshRecords();
    }

    public function addRecord()
    {
        //get the latest student record first
        $latestRecord = StudentRecord::join('years', 'years.id', '=', 'student_records.year_id')
            ->where('person_id', $this->person->id)
            ->orderBy('years.year_start', 'desc')->first();
        if($latestRecord)
        {
            //we need to find the next year. If we lack one, use the current one.
            $year = Year::where('year_start', '>', $latestRecord->year->year_start)->first();
            if(!$year)
                $year = $latestRecord->year;
            $level = Level::where('order', '>', $latestRecord->level->order)->orderBy('order', 'asc')->first();
            if(!$level)
                $level = Level::first();
        }
        else
        {
            //in this case, there's no record, so we create one for the current year
            $year = Year::currentYear();
            $level = Level::first();
        }
        $newRecord = new StudentRecord();
        $newRecord->year_id = $year->id;
        $newRecord->level_id = $level->id;
        $newRecord->campus_id = $level->campuses()->first()->id;
        $newRecord->start_date = $year->year_start;
        $this->person->studentRecords()->save($newRecord);
        $this->person->refresh();
        $this->refreshRecords();
    }

    public function updateYear(StudentRecord $studentRecord, Year $year)
    {
        $studentRecord->year_id = $year->id;
        $studentRecord->start_date = $year->year_start;
        $studentRecord->save();
        $this->refreshRecords();
    }

    public function updateLevel(StudentRecord $studentRecord, Level $level)
    {
        $studentRecord->level_id = $level->id;
        $studentRecord->campus_id = $level->campuses()->first()->id;
        $studentRecord->save();
        $this->refreshRecords();
    }

    public function updateCampus(StudentRecord $studentRecord, Campus $campus)
    {
        $studentRecord->campus_id = $campus->id;
        $studentRecord->save();
        $this->refreshRecords();
    }

    public function updateStartDate(StudentRecord $studentRecord, string $startDate)
    {
        if($startDate >= $studentRecord->year->year_start && $startDate <= $studentRecord->year->year_end)
        {
            $studentRecord->start_date = $startDate;
            $studentRecord->save();
        }
        else
        {
            $this->addError('startDate-' . $studentRecord->id, 'Start date must be between the start and end dates of the year.');
        }
        $this->refreshRecords();
    }

    public function withdrawStudent(StudentRecord $studentRecord)
    {
        //figure out a date
        $date = date('Y-m-d');
        if($date < $studentRecord->year->year_start && $date > $studentRecord->year->year_end)
            $date = $studentRecord->year->year_end;
        $studentRecord->end_date = $date;
        $studentRecord->save();
        $this->refreshRecords();
    }

    public function updateDismissalReason(StudentRecord $studentRecord, DismissalReason $dismissalReason)
    {
        if($studentRecord->end_date)
        {
            $studentRecord->dismissal_reason_id = $dismissalReason->id;
            $studentRecord->save();
            $this->refreshRecords();
        }
    }

    public function updateEndDate(StudentRecord $studentRecord, string $endDate)
    {
        if($endDate >= $studentRecord->year->year_start && $endDate <= $studentRecord->year->year_end)
        {
            $studentRecord->end_date = $endDate;
            $studentRecord->save();
        }
        else
        {
            $this->addError('endDate-' . $studentRecord->id, 'End date must be between the start and end dates of the year.');
        }
        $this->refreshRecords();
    }

    public function updateDismissalNote(StudentRecord $studentRecord, string $dismissalNote)
    {
        $studentRecord->dismissal_note = $dismissalNote;
        $studentRecord->save();
        $this->refreshRecords();
    }

    public function undoWithdrawal(StudentRecord $studentRecord)
    {
        $studentRecord->end_date = null;
        $studentRecord->dismissal_reason_id = null;
        $studentRecord->dismissal_note = null;
        $studentRecord->save();
        $this->refreshRecords();
    }

    public function deleteRecord(StudentRecord $studentRecord)
    {
        if($studentRecord->canRemove())
            $studentRecord->delete();
        $this->refreshRecords();
    }

    public function render()
    {
        return view('livewire.people.student-record-manager');
    }
}
