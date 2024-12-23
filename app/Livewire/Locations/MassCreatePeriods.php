<?php

namespace App\Livewire\Locations;

use App\Classes\Days;
use App\Models\Locations\Campus;
use App\Models\Schedules\Period;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MassCreatePeriods extends Component
{
    public Campus $campus;
    public Collection $availableCampuses;
    public string $tab = 'copy_campus';

    //copy to another campus fields
    public int $copy_day = Days::ALL;
    public int $copy_campus_id;
    public array $copyPeriods;

    //mass create
    public int $create_day = Days::MONDAY;
    public string $create_start = "08:00";
    public string $create_end = "15:00";
    public int $create_duration = 50;
    public int $create_between = 10;
    public array $createPeriods;

    //copy to day
    public int $copy_to_day = Days::TUESDAY;
    public int $copy_from_day = Days::MONDAY;
    public array $copyToDay;


    public function mount(Campus $campus)
    {
        $this->campus = $campus;
        $this->availableCampuses = Auth::user()->employeeCampuses()
            ->where('campus_id', '<>', $this->campus->id)
            ->get();
        $this->copy_campus_id = $this->availableCampuses->first()->id;
        $this->updateCopyPeriods();
        $this->updateCopyToDay();
        $this->updateMassCreate();
    }

    public function updateCopyPeriods()
    {
        if($this->copy_day == Days::ALL)
            $periods = $this->campus->periods()->active()->get();
        else
            $periods = $this->campus->periods($this->copy_day)->active()->get();
        $this->copyPeriods = [];
        foreach($periods as $period)
        {
            $this->copyPeriods[$period->id] =
                [
                    'id' => $period->id,
                    'name' => $period->name,
                    'abbr' => $period->abbr,
                    'day' => $period->day,
                    'start' => $period->start->format('H:i'),
                    'end' => $period->end->format('H:i'),
                    'selected' => true,
                ];
        }
    }

    public function updateCopyToDay()
    {
        $periods = $this->campus->periods($this->copy_from_day)->active()->get();
        $this->copyToDay = [];
        $idx = 1;
        foreach($periods as $period)
        {
            $this->copyToDay[$period->id] =
                [
                    'id' => $period->id,
                    'name' => Days::day($this->copy_to_day) . " " . $idx,
                    'abbr' => Days::dayAbbr($this->copy_to_day) . $idx,
                    'day' => $this->copy_to_day,
                    'start' => $period->start->format('H:i'),
                    'end' => $period->end->format('H:i'),
                    'selected' => true,
                ];
            $idx++;
        }
    }

    public function updateMassCreate()
    {
        $this->createPeriods = [];
        $idx = 1;
        $start = Carbon::createFromFormat('H:i', $this->create_start);
        $end = Carbon::createFromFormat('H:i', $this->create_end);
        while($start->lt($end))
        {
            $this->createPeriods[$idx] =
                [
                    'selected' => true,
                    'id' => $idx,
                    'name' => Days::day($this->create_day) . " " . $idx,
                    'abbr' => Days::dayAbbr($this->create_day) . $idx,
                    'day' => $this->create_day,
                    'start' => $start->format('H:i'),
                    'end' => $start->addMinutes($this->create_duration)->format('H:i'),
                ];
            $start->addMinutes($this->create_between);
            $idx++;
        }
    }

    public function doCopy()
    {
        foreach($this->copyPeriods as $period)
        {
            if($period['selected'])
            {
                $newPeriod = new Period();
                $newPeriod->name = $period['name'];
                $newPeriod->abbr = $period['abbr'];
                $newPeriod->day = $period['day'];
                $newPeriod->start = $period['start'];
                $newPeriod->end = $period['end'];
                $newPeriod->campus_id = $this->copy_campus_id;
                $newPeriod->save();
            }
        }
        return redirect()->route('locations.campuses.show', $this->copy_campus_id);
    }

    public function doCopyToDay()
    {
        foreach($this->copyToDay as $period)
        {
            if($period['selected'])
            {
                $newPeriod = new Period();
                $newPeriod->name = $period['name'];
                $newPeriod->abbr = $period['abbr'];
                $newPeriod->day = $period['day'];
                $newPeriod->start = $period['start'];
                $newPeriod->end = $period['end'];
                $newPeriod->campus_id = $this->campus->id;
                $newPeriod->save();
            }
        }
        return redirect()->route('locations.campuses.show', $this->campus);
    }


    public function massCreate()
    {
        foreach($this->createPeriods as $period)
        {
            if($period['selected'])
            {
                $newPeriod = new Period();
                $newPeriod->name = $period['name'];
                $newPeriod->abbr = $period['abbr'];
                $newPeriod->day = $period['day'];
                $newPeriod->start = $period['start'];
                $newPeriod->end = $period['end'];
                $newPeriod->campus_id = $this->campus->id;
                $newPeriod->save();
            }
        }
        return redirect()->route('locations.campuses.show', $this->campus);
    }

    public function render()
    {
        return view('livewire.locations.mass-create-periods');
    }
}
