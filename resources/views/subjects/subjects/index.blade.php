@extends('layouts.app', ['breadcrumb' => $breadcrumb])
@push('head_scripts')
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable/build/umd/index.min.js"></script>
@endpush
@section('content')
    <div class="container">
        <div class="border-bottom d-flex justify-content-between align-items-baseline mb-3 pb-2" id="add-header">
            <div class="input-group w-50">
                <label for="campus_id" class="input-group-text">{{ __('subjects.subject.viewing') }}</label>
                <select
                        class="form-select"
                        id="campus_id"
                        name="campus_id"
                        onchange="window.location.href = '/academics/subjects/' + this.value"
                >
                    @foreach(Auth::user()->employeeCampuses as $campusOption)
                        <option value="{{ $campusOption->id }}"
                                @if($campusOption->id == $campus->id) selected @endif>{{ $campusOption->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-check form-switch">
                <input
                        class="form-check-input"
                        type="checkbox"
                        role="switch"
                        id="show-inactive"
                        onclick="$('.subject.inactive').toggleClass('d-none')"
                >
                <label class="form-check-label" for="show-inactive">{{ __('subjects.subject.inactive.show') }}</label>
            </div>
            <button
                    class="btn btn-primary ms-3"
                    onclick="$('#add-container,#add-header').toggleClass('d-none')"
                    type="button"
            >
                <i class="fa fa-plus pe-1 me-1 border-end"></i>
                {{ __('subjects.subject.add') }}
            </button>
        </div>
        <div class="card mb-2 text-bg-light d-none" id="add-container">
            <form action="{{ route('subjects.subjects.store', ['campus' => $campus->id]) }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <label for="name" class="form-label">{{ __('subjects.subject.name') }}</label>
                            <input
                                    type="text"
                                    class="form-control @error('name') is-invalid @enderror"
                                    name="name"
                                    id="name"
                            />
                            <x-error-display key="name">{{ $errors->first('name') }}</x-error-display>
                        </div>
                        <div class="col-2">
                            <label for="abbr" class="form-label">{{ __('subjects.subject.color') }}</label>
                            <input type="color" class="form-control" name="color" id="color"/>
                        </div>
                        <div class="col-2 align-self-end text-center">
                            <button type="submit" class="btn btn-primary">{{ __('subjects.subject.add') }}</button>
                            <button
                                    type="button"
                                    onclick="$('#add-container,#add-header').toggleClass('d-none')"
                                    class="btn btn-secondary"
                            >{{ __('common.cancel') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="row fw-bold fs-6">
            <div class="col-sm-1"></div>
            <div class="col-sm-4">
                {{ __('subjects.subject.name') }}
            </div>
            <div class="col-sm-2 text-center">
                {{ trans_choice('subjects.subject.courses', 2) }}
            </div>
            <div class="col-sm-2 text-center">
                {{ __('subjects.subject.terms') }}
            </div>
        </div>
        <ul class="list-group subject-list">
            @foreach($campus->subjects as $subject)
                <li
                        class="subject list-group-item @if(!$subject->active) inactive d-none opacity-50 @endif"
                        style="background-color: {{ $subject->color }}; color: {{ $subject->getTextHex() }}"
                        subject-id="{{ $subject->id }}"
                >
                    <div class="row" subject-id="{{ $subject->id }}">
                        <span class="col-sm-1 align-self-center text-start sort-handle"><i
                                    class="fa-solid fa-grip-lines-vertical"></i></span>
                        <span class="col-sm-4 align-self-center">{{ $subject->name }}</span>
                        <span class="col-sm-2 align-self-center text-center">
                            <a
                                    href="{{ route('subjects.courses.index', ['subject' => $subject->id]) }}"
                                    class="fw-bold"
                                    style="color: {{ $subject->getTextHex() }} !important"
                            >
                                {{ $subject->courses()->count() }} {{ trans_choice('subjects.course', $subject->courses()->count()) }}
                            </a>
                        </span>
                        <span class="col-sm-2 align-self-center text-center">{{ $subject->required_terms?? __('common.na') }}</span>
                        <div class="col-sm-3 align-self-center text-end">
                            <a
                                    href="{{ route('subjects.subjects.edit', ['subject' => $subject->id]) }}"
                                    class="btn btn-primary"
                            ><i class="fa-solid fa-edit"></i></a>
                            @can('delete', $subject)
                                <button
                                        onclick="confirmDelete('{{ __('subjects.subject.delete.confirm') }}', '{{ route('subjects.subjects.destroy', ['subject' => $subject->id]) }}')"
                                        class="btn btn-danger"
                                ><i class="fa-solid fa-times"></i></button>
                            @endcan
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
@push('scripts')
    <script>
        const sortable = new Draggable.Sortable(document.querySelectorAll('.subject-list'), {
            draggable: '.subject',
            handle: '.sort-handle',
            mirror: {
                constrainDimensions: true
            }
        });
        sortable.on('sortable:stop', () => {
            let sorted = [];
            $('.subject:not(.draggable--original):not(.draggable-mirror)').each((index, subject) => {
                sorted.push($(subject).attr('subject-id'))
            });

            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('subjects.subjects.update.order') }}';
            var putInput = document.createElement('input');
            putInput.type = 'hidden';
            putInput.name = '_method';
            putInput.value = 'PUT';
            form.appendChild(putInput);
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = jQuery('meta[name="csrf-token"]').attr('content');
            form.appendChild(csrf);
            var sortInput = document.createElement('input');
            sortInput.type = 'hidden';
            sortInput.name = 'subjects';
            sortInput.value = JSON.stringify(sorted);
            form.appendChild(sortInput);
            document.body.appendChild(form);
            form.submit();
        })
    </script>
@endpush
