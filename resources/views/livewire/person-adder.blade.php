<div class="container">
    <form wire:submit="addPerson">
        <div class="row">
            <div class="col-md-6">
                <div class="form-floating mb-0">
                    <input
                        type="text"
                        class="form-control"
                        id="first"
                        placeholder="{{ __('people.profile.fields.first') }}"
                        autocomplete="off"
                        wire:model.live.debounce="first"
                    />
                    <label for="first">{{ __('people.profile.fields.first') }}</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-floating mb-0">
                    <input
                        type="text"
                        class="form-control @error('last') is-invalid @enderror"
                        id="last"
                        placeholder="{{ __('people.profile.fields.last') }}"
                        autocomplete="off"
                        wire:model.live.debounce="last"
                    />
                    <label for="first">{{ __('people.profile.fields.last') }}</label>
                </div>
                <x-error-display key="last">{{ $errors->first('last') }}</x-error-display>
            </div>
            <div class="col-md-12 mt-3">
                <div class="form-floating mb-0">
                    <input
                        type="text"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        placeholder="{{ __('people.profile.fields.email') }}"
                        autocomplete="off"
                        wire:model.live.debounce="email"
                    />
                    <label for="email">{{ __('people.profile.fields.email') }}</label>
                </div>
                <x-error-display key="email">{{ $errors->first('email') }}</x-error-display>
            </div>
        </div>
        <div class="row-cols-auto row-cols-auto mt-3">
            <div class="col-12 text-end">
                <button
                    type="submit"
                    class="btn btn-primary"
                >{{ __('people.add_person') }}</button>
            </div>
        </div>
    </form>

    @if($suggestedPeople && $suggestedPeople->count() > 0)
        <div class="mt-3">
            <h2>{{ __('people.are_you_trying_to_add_any_of_these_people') }}</h2>
            <ul class="list-group">
                @foreach($suggestedPeople as $suggestion)
                    <li class="list-group-item list-group-item-action" wire:key="{{ $suggestion->id }}" wire:click="showPerson({{$suggestion->id}})">
                        <div class="row">
                            <div class="col-md-2">
                                <img
                                    class='img-thumbnail'
                                    src='{{ $suggestion->thumbnail_url }}'
                                    style="height: {{ config('lms.thumb_max_height') }}px !important;"
                                    alt='{{ __('people.profile.image') }}'
                                />
                            </div>
                            <h3 class="col-md-7 align-self-center ">{{ $suggestion->name }}</h3>
                            <div class="col-3 align-self-center text-end">
                                @if($suggestion->isStudent())
                                    <span class="badge text-bg-primary">{{ __('common.student') }}</span>
                                @endif
                                @if($suggestion->isParent())
                                    <span class="badge text-bg-primary">{{ __('common.parent') }}</span>
                                @endif
                                @if($suggestion->isEmployee())
                                    <span class="badge text-bg-primary">{{ trans_choice('people.employee', 1) }}</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

</div>
