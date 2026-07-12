<ul class="list-group list-group-flush hoverable">
    @if($person->first)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-center">
                <label>{{ __('people.profile.fields.first') }}</label>
                <span>{{ $person->first }}</span>
            </div>
        </li>
    @endif
    @if($person->middle)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-center">
                <label>{{ __('people.profile.fields.middle') }}</label>
                <span>{{ $person->middle }}</span>
            </div>
        </li>
    @endif
    @if($person->last)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-center">
                <label>{{ __('people.profile.fields.last') }}</label>
                <span>{{ $person->last }}</span>
            </div>
        </li>
    @endif
    @if($person->email)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-center">
                <label>{{ __('people.profile.fields.email') }}</label>
                <span>{{ $person->email }}</span>
            </div>
        </li>
    @endif
    @if($person->nick)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-center">
                <label>{{ __('people.profile.fields.nick') }}</label>
                <span>{{ $person->nick }}</span>
            </div>
        </li>
    @endif
    @if($person->dob)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-center">
                <label>{{ __('people.profile.fields.dob') }}</label>
                <span>{{ $person->dob->format(config('lms.date_format')) }}</span>
            </div>
        </li>
    @endif
    <!-- Role Fields -->
    @foreach($person->role_fields?->availableFields() ?? [] as $fieldKey => $fieldName)
        @continue(!$person->role_fields->{$fieldKey})
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-center">
                <label>{{ $fieldName }}</label>
                <span>{{ $person->role_fields->{$fieldKey} }}</span>
            </div>
        </li>
    @endforeach
    @if($person->primaryAddress)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-top">
                <label>
                    {{ __('addresses.primary_address') }}:
                </label>
                <span class="text-end">{!! nl2br($person->primaryAddress->prettyAddress) !!}</span>
            </div>
        </li>
    @endif
    @foreach($person->addresses ?? [] as $address)
        @continue($address->personal->primary)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-top">
                <label>
                    {{ $address->personal->label . " " . __('addresses.address') }}:
                </label>
                <span class="text-end">{!! nl2br($address->prettyAddress) !!}</span>
            </div>
        </li>
    @endforeach
    @if($person->primaryPhone)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-top">
                <label>
                    {{ __('phones.primary_phone') }}
                </label>
                <span>{!! $person->primaryPhone->prettyPhone !!}</span>
            </div>
        </li>
    @endif
    @foreach($person->phones ?? [] as $phone)
        @continue($phone->personal->primary)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-top">
                <label>
                    {{ $phone->personal->label . " " . __('phones.phone') }}:
                </label>
                <span>{!! $phone->prettyPhone !!}</span>
            </div>
        </li>
    @endforeach
    @foreach($person->relationships ?? [] as $relation)
        <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
            <div class="d-flex justify-content-between align-items-top">
                <label>
                    {{ ($relation->personal->relationship? $relation->personal->relationship->name: "?") . " " . __('common.to') }}
                </label>
                <span>
                    <a href="{{ route('people.show', ['person' => $relation->school_id]) }}">
                        {{ $relation->name }}
                    </a>
                </span>
            </div>
        </li>
    @endforeach
</ul>