<?php

namespace App\View\Components;

use App\Classes\Settings\SchoolSettings;
use App\Interfaces\HasSchedule;
use App\Models\Schedules\Period;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\View\Component;

class ScheduleViewer extends Component
{
    public Collection $scheduleSources;
    public array $schedule;
    public int $width = 600;
    public int $height = 800;
    public Carbon $start;
    public Carbon $end;
    public int $totalHours = 8;
    public int $totalDays = 5;

    //dimensions
    public int $headerHeight = 50;
    public int $hourHeight = 100;
    public int $hourWidth = 50;
    public int $bodyHeight = 750;

    /**
     * Create a new component instance.
     */
    public function __construct(Collection|array|HasSchedule $scheduleSources, int $width = 600, int $height = 800 )
    {
        if(is_array($scheduleSources))
            $this->scheduleSources = collect($scheduleSources);
        elseif($scheduleSources instanceof Collection)
            $this->scheduleSources = $scheduleSources;
        else
            $this->scheduleSources = collect([$scheduleSources]);
        $this->width = $width;
        $this->height = $height;
        $settings = App::make(SchoolSettings::class);
        $this->start = Carbon::parse($settings->startTime);
        $this->end = Carbon::parse($settings->endTime)->addHour();
        $this->totalHours = $this->start->diffInHours($this->end) + 1;
        $this->totalDays = count($settings->days);
        //some calculations
        $this->bodyHeight = $this->height - $this->headerHeight;
        $this->hourHeight = (int)floor($this->bodyHeight / $this->totalHours);
        $this->hourWidth = (int)floor(($this->width /($this->totalDays + 1)));
        //next, we parse the schedule.
        $this->schedule = [];
        foreach($settings->days as $dayId => $dayName)
            $this->schedule[$dayId] = [];
        foreach($this->scheduleSources as $source)
        {
            $periods = $source->getSchedule();
            $label = $source->getScheduleLabel();
            $color = $source->getScheduleColor();
            $text = $source->getScheduleTextColor();
            $link = $source->getScheduleLink();
            foreach($periods as $period)
            {
                $event =
                    [
                        'period' => $period,
                        'label' => $label,
                        'color' => $color,
                        'text' => $text,
                        'link' => $link,
                    ];
                $this->schedule[$period->day][] = $event;
            }
        }
    }

    /**
     * This function will return true if the NOW bar should be rendered on this day. This depends on 2 things:
     *  - That the day being passed is today
     *  - That we're still within school hours.
     * @param int $dayId The day that we're checking to see if we need to draw the "NOW" bar in
     * @return bool Whether we should be drawing the "NOW" bar.
     */
    public function hasNow(int $dayId): bool
    {
        //first, we check the day
        if(date('N') != $dayId)
            return false;
	    $settings = App::make(SchoolSettings::class);
        // since we're in the right day, are we between the right times?
        $start = Carbon::createFromTimeString($settings->startTime);
        $end = Carbon::createFromTimeString($settings->endTime);
        $now = Carbon::now();
        $now = Carbon::createFromTimeString("10:15");
        return $now->between($start, $end);
    }

    public function getNowTop()
    {
        $now = Carbon::now();
        $now = Carbon::createFromTimeString("10:15");
	    $settings = App::make(SchoolSettings::class);
        return floor(abs($this->hourHeight * $now->diffInHours($settings->startTime))) - 1;
    }

    public function getEventHeight(Period $period): int
    {
        return floor(abs($this->hourHeight * $period->start->diffInHours($period->end)));
    }

    public function getEventTop(Period $period): int
    {
	    $settings = App::make(SchoolSettings::class);
        return floor(abs($this->hourHeight * $period->start->diffInHours($settings->startTime))) - 1;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.schedule-viewer');
    }
}
