@extends('layouts.app', ['breadcrumb' => $breadcrumb])

@section('content')
    <div class="container pb-5">
        <form method="POST" action="{{ route('subjects.subjects.update', $subject) }}">
            @csrf
            @method('PUT')
            <div class="d-flex justify-content-between align-items-end border-bottom pb-3 mb-4">
                <h1 class="h2 mb-0 text-primary">
                    <i class="fas fa-edit me-2"></i>{{ __('subjects.subject.edit') }}
                </h1>
                <a href="{{ route('subjects.subjects.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ __('common.cancel') }}
                </a>
            </div>

            <div class="row g-4">
                <div class="col-lg-5 col-xl-4">
                    <div class="card shadow-sm border-primary border-top border-3 h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="m-0 fw-bold text-primary">{{ __('subjects.subject.edit') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label for="name"
                                       class="form-label fw-bold text-dark">{{ __('subjects.subject.name') }}</label>
                                <input
                                        type="text"
                                        class="form-control form-control-lg shadow-sm @error('name') is-invalid @enderror"
                                        id="name"
                                        name="name"
                                        value="{{ $subject->name }}"
                                />
                                <x-utilities.error-display
                                        key="name">{{ $errors->first('name') }}</x-utilities.error-display>
                            </div>

                            <div class="mb-4">
                                <div class="form-check form-switch fs-5">
                                    <input class="form-check-input" type="checkbox" role="switch" id="active"
                                           name="active" @if($subject->active) checked @endif>
                                    <label class="form-check-label ms-2 pt-1 fw-bold text-dark"
                                           for="active">{{ __('subjects.subject.active') }}</label>
                                </div>
                            </div>

                            <div class="row mb-4 g-3">
                                <div class="col-sm-6">
                                    <label class="form-label fw-bold text-dark"
                                           for="color">{{ __('subjects.subject.color') }}</label>
                                    <input type="color" class="form-control form-control-color w-100 shadow-sm"
                                           id="color" name="color" value="{{ $subject->color }}"
                                           title="Choose subject color"/>
                                </div>
                                <div class="col-sm-6">
                                    <label for="required_terms"
                                           class="form-label fw-bold text-dark">{{ __('subjects.subject.required_terms') }}</label>
                                    <input type="number" class="form-control shadow-sm" id="required_terms"
                                           name="required_terms" value="{{ $subject->required_terms ?? 0 }}" min="0"/>
                                </div>
                            </div>

                            <hr class="text-muted mb-4">

                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg shadow-sm" type="submit">
                                    <i class="fas fa-save me-2"></i>{{ trans_choice('subjects.subject.update', 1) }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7 col-xl-8">
                    <div class="card shadow-sm border-info border-top border-3 h-100">
                        <div class="card-header bg-white py-3">
                            <h5 class="m-0 fw-bold text-info">
                                <i class="fas fa-chalkboard-teacher me-2"></i>{{ trans_choice('subjects.subject.teachers', 2) }}
                            </h5>
                        </div>
                        <div class="card-body bg-light rounded-bottom">
                            <livewire:people.create-roster instance="teachers"
                                                           :roles-filter="[\App\Models\Utilities\SchoolRoles::$FACULTY]"
                                                           :person_ids="$subject->teachers->pluck('id')->toArray()"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
