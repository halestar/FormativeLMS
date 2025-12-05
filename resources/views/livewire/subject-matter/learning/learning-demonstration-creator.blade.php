<div class="container">
    <h3 class="border-bottom mb-3">{{ __('learning.demonstrations.new') }}</h3>
    <form wire:submit="create">
        <div class="row mb-3">
            <div class="col-md-8">
                <label for="name" class="form-label">{{ __('learning.demonstrations.name') }}</label>
                <input
                        type="text"
                        id="name"
                        wire:model="name"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="{{ __('learning.demonstrations.name') }}"
                        aria-describedby="name_help"
                />
                <x-utilities.error-display key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                <div id="name_help"
                     class="form-text">{!! __('learning.demonstrations.name.description') !!}</div>
            </div>
            <div class="col-md-4">
                <label for="abbr" class="form-label">{{ __('learning.demonstrations.abbr') }}</label>
                <input
                        type="text"
                        id="abbr"
                        wire:model="abbr"
                        class="form-control @error('abbr') is-invalid @enderror"
                        placeholder="{{ __('learning.demonstrations.abbr') }}"
                        aria-describedby="abbr_help"
                />
                <x-utilities.error-display key="abbr">{{ $errors->first('abbr') }}</x-utilities.error-display>
                <div id="abbr_help"
                     class="form-text">{!! __('learning.demonstrations.abbr.description') !!}</div>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label for="course" class="form-label">{{ __('learning.demonstrations.course') }}</label>
                <select class="form-select @error('course') is-invalid @enderror" id="course" wire:model="selectedCourseId" aria-describedby="course_help"
                        wire:change="setCourse">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                    @endforeach
                </select>
                <x-utilities.error-display key="course">{{ $errors->first('course') }}</x-utilities.error-display>
                <div id="course_help"
                     class="form-text">{!! __('learning.demonstrations.course.description') !!}</div>
            </div>
            <div class="col-md-6">
                <label for="course" class="form-label">{{ __('learning.demonstrations.type') }}</label>
                <select class="form-select" id="type" wire:model="type" aria-describedby="type_help">
                    @foreach($demonstrationTypes as $demonstrationType)
                        <option value="{{ $demonstrationType->id }}">{{ $demonstrationType->name }}</option>
                    @endforeach
                </select>
                <x-utilities.error-display key="type">{{ $errors->first('type') }}</x-utilities.error-display>
                <div id="type_help"
                     class="form-text">{!! __('learning.demonstrations.type.description') !!}</div>
            </div>
        </div>
        <h4 class="border-bottom mb-3">{{ __('learning.demonstrations.strategy.pick') }}</h4>
        <div class="row mb-3 justify-content-center">
            <div class="col-md-6 text-center">
                <input
                        type="radio"
                        class="btn-check @error('strategy') is-invalid @enderror"
                        name="strategy"
                        id="strategy_assessment"
                        value="assessment"
                        wire:model.live="strategy"
                />
                <label class="btn btn-outline-info"
                       for="strategy_assessment">{{ __('learning.demonstrations.strategy.assessment') }}</label>
                <div class="form-text">{!! __('learning.demonstrations.strategy.assessment.description') !!}</div>
            </div>
            <div class="col-md-6 text-center">
                <input
                        type="radio"
                        class="btn-check @error('strategy') is-invalid @enderror"
                        name="strategy"
                        id="strategy_demonstration"
                        value="demonstration"
                        wire:model.live="strategy"
                />
                <label class="btn btn-outline-info"
                       for="strategy_demonstration">{{ __('learning.demonstrations.strategy.demonstration') }}</label>
                <div class="form-text">{!! __('learning.demonstrations.strategy.demonstration.description') !!}</div>
            </div>
        </div>
        @error('strategy')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror
        @if($strategy == "assessment")
            <h4 class="border-bottom mb-3">{{ __('learning.demonstrations.skills') }}</h4>
            @error('skills')
                <div class="alert alert-danger my-2">{{ $message }}</div>
            @enderror
            <livewire:assessment.skill-selector :course="$courses[$selectedCourseId]"  wire:model.live="skills"/>
        @elseif($strategy == "demonstration")
            <h4 class="border-bottom mb-3">{{ __('learning.demonstrations.demonstration') }}</h4>
            @error('demonstration')
            <div class="alert alert-danger my-2">{{ $message }}</div>
            @enderror
            <div class="row">
                <div class="col-md-9 p-2">
                    <livewire:utilities.text-editor
                            instance-id="demonstration"
                            :fileable="$filer"
                            wire:model.live="demonstration"
                    />
                </div>
                <div class="col-md-3 p-2">
                    <livewire:storage.work-storage-browser
                            :fileable="$filer"
                            :title="__('learning.demonstrations.files')"
                            classes="col-md-4"
                    />
                </div>

            </div>
        @endif
        <div class="row mt-4">
            <button type="submit" class="btn btn-primary">{{ __('learning.demonstrations.create') }}</button>
        </div>
    </form>
    @if($errors->any())
        <ul>
            @foreach($errors->all() as $error)
                <li class="alert alert-danger">{{ $error }}</li>
            @endforeach
        </ul>
        @endif
</div>
