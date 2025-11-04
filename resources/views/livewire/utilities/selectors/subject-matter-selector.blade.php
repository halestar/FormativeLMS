<div class="input-group {{ $classes }}">
    <span class="input-group-text">{{ trans_choice('locations.campus',1) }}</span>
    <select class="form-select" wire:model="selectedCampusId" wire:change="setCampus()" name="campus_id" id="campus_id">
        @foreach($campuses as $campus)
            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
        @endforeach
    </select>
    <span class="input-group-text">{{ trans_choice('subjects.subject',1) }}</span>
    <select class="form-select" wire:model="selectedSubjectId" name="subject_id" id="subject_id">
        @foreach($subjects as $subject)
            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
        @endforeach
    </select>
</div>
