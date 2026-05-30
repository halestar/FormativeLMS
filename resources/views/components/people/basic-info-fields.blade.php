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
    @if($self->canViewField('addresses', $person))
        @foreach($person->addresses as $address)
            <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
                <div class="d-flex justify-content-between align-items-top">
                    <label>
                        @if($address->personal->primary)
                            {{ __('addresses.primary') }}
                        @endif
                        @if($address->personal->work)
                            {{ __('addresses.work') }}
                        @endif
                        @if($address->personal->seasonal)
                            {{ __('addresses.seasonal_address', ['season_start' => $address->personal->season_start, 'season_end' => $address->personal->season_end]) }}
                        @endif
                        {{ __('addresses.address') }}:
                    </label>
                    <span class="text-end">{!! nl2br($address->prettyAddress) !!}</span>
                </div>
            </li>
        @endforeach
    @endif
    @if($self->canViewField('phones', $person))
        @foreach($person->phones as $phone)
            <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
                <div class="d-flex justify-content-between align-items-top">
                    <label>
                        @if($phone->personal->primary)
                            {{ __('addresses.primary') }}
                        @endif
                        @if($phone->personal->work)
                            {{ __('addresses.work') }}
                        @endif
                        @if($phone->mobile)
                            {{ __('phones.mobile') }}
                        @endif
                        {{ __('phones.phone') }}:
                    </label>
                    <span>{!! $phone->prettyPhone !!}</span>
                </div>
            </li>
        @endforeach
    @endif
    @if($self->canViewField('relationships', $person))
        @foreach($person->relationships as $relation)
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
    @endif
    <!-- Role Fields -->
    @foreach($person->schoolRoles as $role)
        @if(count($role->fields) > 0)
            @foreach($role->fields as $field)
                @if($self->canViewField($field, $person) && $field->fieldValue)
                    <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
                        <div class="d-flex justify-content-between align-items-center">
                            <label>{{ $field->fieldName }}</label>
                            <span>{{ is_array($field->fieldValue)? implode(", ", $field->fieldValue): $field->fieldValue }}</span>
                        </div>
                    </li>
                @endif
            @endforeach
        @endif
    @endforeach
</ul>