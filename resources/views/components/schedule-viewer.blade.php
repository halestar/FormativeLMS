<div class="schedule-viewer" style="height: {{ $height }}px; width: {{ $width }}px;">

    <div class="schedule-header" style="height: {{ $headerHeight }}px;">
        <div class="schedule-empty" style="width: {{ $hourWidth }}px; height: 100%;"></div>
        @foreach(\App\Classes\Days::schoolDaysOptions() as $dayId => $day)
            <div class="schedule-header-day"
                 style="width: {{ $hourWidth }}px; line-height: {{ $headerHeight }}px; left: {{ $loop->iteration * $hourWidth }}px">{{ $day }}</div>
        @endforeach
    </div>

    <div class="schedule-body" style="height: {{ $bodyHeight }}px;">
        <div class="schedule-times" style="width: {{ $hourWidth }}px;">
            @for($time = $start->copy(); $time->lte($end); $time->addHour())
                <div class="schedule-time-slot"
                     style="height: {{ $hourHeight }}px; line-height: {{ $hourHeight }}px;">{{ $time->format('h:i A') }}</div>
            @endfor
        </div>
        @foreach(\App\Classes\Days::schoolDaysOptions() as $dayId => $day)
            <div class="schedule-day-column @if($loop->first) first @endif"
                 style="width: {{ $hourWidth }}px; height: 100%; left: {{ $loop->iteration * $hourWidth }}px">
                @for($time = $start->copy(); $time->lte($end); $time->addHour())
                    <div class="schedule-day" style="height: {{ $hourHeight }}px;"></div>
                @endfor
                @foreach($schedule[$dayId] as $event)
                    <div class="schedule-event"
                         style="height: {{ $getEventHeight($event['period']) }}px; top: {{ $getEventTop($event['period']) }}px; left: 0; width: {{ $hourWidth }}px; background-color: {{ $event['color'] }}; color: {{ $event['text'] }} !important;">
                        <a href="{{ $event['link']?? "#none" }}" class="text-reset text-decoration-none">
                            <div class="schedule-event-start">
                                {{ $event['period']->start->format('h:i') }}
                            </div>
                            <div class="schedule-event-label"
                                 style="line-height: {{ $getEventHeight($event['period']) }}px;">
                                <i>{{ $event['label'] }}</i>
                            </div>
                            <div class="schedule-event-end">
                                {{ $event['period']->end->format('h:i') }}
                            </div>
                        </a>
                    </div>
                @endforeach
                @if($hasNow($dayId))
                    <div class="now-bar"
                         style="top: {{ $getNowTop() }}px; width: {{ $hourWidth }}px;">{{ __('common.now') }}</div>
                @endif
            </div>
        @endforeach
    </div>
</div>
