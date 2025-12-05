<div class="d-flex flex-column {!!  $classes !!}" @if($style) style="{{ $style }}" @endif>
    @use('App\Enums\ClassViewer')
    @if($session && $student)
        <div class="d-flex justify-content-between align-items-center border-bottom chat-header flex-wrap">
            <div class="mb-3">
                <strong class="text-decoration-underline mb-3">{{ __('common.legend') }}: </strong>
                <div class="d-flex flex-row">
                    <div class="me-2 d-flex flex-row align-items-center"><span class="bg-success-subtle legend-box me-1">&nbsp;</span>:
                        Self
                    </div>
                    <div class="me-2 d-flex flex-row align-items-center"><span
                                class="from-student legend-box me-1">&nbsp;</span>: Student
                    </div>
                    <div class="me-2 d-flex flex-row align-items-center"><span
                                class="from-parent legend-box me-1">&nbsp;</span>: Parent
                    </div>
                    <div class="me-2 d-flex flex-row align-items-center"><span
                                class="from-teacher legend-box me-1">&nbsp;</span>: Teacher
                    </div>
                </div>
            </div>

            @if($messages->count() > 0)
                <div>
                    {{ $messages->links(data:['reverseArrows' => true]) }}
                </div>
            @endif
        </div>
        <div class="pt-3 pe-3 message-container">
            @foreach($messages->reverse() as $message)
                @if($message->postedBy && $message->postedBy->id == $self->id)
                    <div
                            class="d-flex flex-row justify-content-start"
                            wire:key="{{ $message->id }}"
                            @if($loop->last)
                                x-data x-init="$el.scrollIntoView()"
                            @endif
                    >
                        <img src="{{ $self->thumbnail_url }}"
                             alt="{{ $self->name }}" class="rounded-circle avatar-img-large">
                        <div class="ms-3 flex-grow-1">
                            <p class="small p-2 mb-1 rounded-3 bg-success-subtle">
                                {{ $message->message }}
                            </p>
                            <p class="small mb-3 rounded-3 text-muted float-end">
                                {{ $message->created_at->format('h:i A') }} | {{ $message->created_at->format('M j Y') }}
                            </p>
                        </div>
                    </div>
                @else
                    <div
                            class="d-flex flex-row justify-content-end"
                            wire:key="{{ $message->id }}"
                            @if($loop->last)
                                x-data x-init="$el.scrollIntoView()"
                            @endif
                    >
                        <div class="me-3 flex-grow-1">
                            <p class="small p-2 mb-1 rounded-3 @if($message->from_type == ClassViewer::STUDENT) from-student @elseif($message->from_type == ClassViewer::PARENT) from-parent @elseif($message->from_type == ClassViewer::FACULTY) from-teacher @else  from-admin @endif">
                                {{ $message->message }}
                            </p>
                            <p class="small mb-3 rounded-3 text-muted float-start">
                                {{ $message->created_at->format('h:i A') }} | {{ $message->created_at->format('M j Y') }}
                            </p>
                        </div>
                        <img src="{{ $message->postedBy? $message->postedBy->thumbnail_url: \App\Models\People\Person::UKN_IMG }}"
                             alt="{{ $message->postedBy? $message->postedBy->name: __('common.unknown') }}"
                             class="rounded-circle avatar-img-large"/>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
