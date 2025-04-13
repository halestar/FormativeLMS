<div class="col-md-6 col-lg-7 col-xl-8 h-100 d-flex flex-column">
    @if($messages->count() > 0)
        <div class="row">
            <div class="col-6">
                {{ $messages->links(data:['scrollTo' => '#latest-msg']) }}
            </div>
            <div class="col-6">
                <strong class="text-decoration-underline mb-2">Legend</strong>
                <div class="d-flex flex-row">
                    <div class="me-2 d-flex flex-row align-items-center"><span class="bg-success-subtle legend-box">&nbsp;</span>: Self</div>
                    <div class="me-2 d-flex flex-row align-items-center"><span class="from-student legend-box">&nbsp;</span>: Student</div>
                    <div class="me-2 d-flex flex-row align-items-center"><span class="from-parent legend-box">&nbsp;</span>: Parent</div>
                    <div class="me-2 d-flex flex-row align-items-center"><span class="from-teacher legend-box">&nbsp;</span>: Teacher</div>
                </div>
            </div>
        </div>
    @endif
    <div class="pt-3 pe-3 message-container flex-grow-1 overflow-auto">
        @foreach($messages->reverse() as $message)
            @if($message->postedBy && $message->postedBy->id == $self->id)
                <div
                    class="d-flex flex-row justify-content-start"
                    wire:key="{{ $message->id }}"
                    @if($loop->last)
                        id="latest-msg"
                    @endif
                >
                    <img src="{{ $self->thumbnail_url }}"
                         alt="{{ $self->name }}" class="person-thumbnail rounded-circle">
                    <div>
                        <p class="small p-2 ms-3 mb-1 rounded-3 bg-success-subtle">
                            {{ $message->message }}
                        </p>
                        <p class="small ms-3 mb-3 rounded-3 text-muted float-end">
                            {{ $message->created_at->format('h:i A') }} | {{ $message->created_at->format('M j Y') }}
                        </p>
                    </div>
                </div>
            @else
                <div
                    class="d-flex flex-row justify-content-end"
                    wire:key="{{ $message->id }}"
                    @if($loop->last)
                        id="latest-msg"
                    @endif
                >
                    <div>
                        <p class="small p-2 me-3 mb-1 rounded-3 @if($message->from_type == \App\Models\SubjectMatter\Components\ClassMessage::FROM_STUDENT) from-student @elseif($message->from_type == \App\Models\SubjectMatter\Components\ClassMessage::FROM_PARENT) from-parent @elseif($message->from_type == \App\Models\SubjectMatter\Components\ClassMessage::FROM_TEACHER) from-teacher @else  from-admin @endif">
                            {{ $message->message }}
                        </p>
                        <p class="small me-3 mb-3 rounded-3 text-muted">
                            {{ $message->created_at->format('h:i A') }} | {{ $message->created_at->format('M j Y') }}
                        </p>
                    </div>
                    <img src="{{ $message->postedBy? $message->postedBy->thumbnail_url: \App\Models\People\Person::UKN_IMG }}"
                         alt="{{ $message->postedBy? $message->postedBy->name: __('common.unknown') }}"
                         class="person-thumbnail rounded-circle" />
                </div>
            @endif
        @endforeach

    </div>
    <div class="text-muted d-flex justify-content-start align-items-center pe-3 pt-3 mt-2">
        <img src="{{ $self->thumbnail_url }}"
             alt="{{ $self->name }}" class="person-thumbnail img-thumbnail rounded-circle me-2">
        <input type="text" class="form-control form-control-lg"
               placeholder="{{ __('subjects.school.message.type') }}" wire:model="newMsg"
               wire:keydown.enter="sendMessage()" />
        <a class="ms-3" href="#" wire:click="sendMessage()"><i class="fas fa-paper-plane"></i></a>
    </div>
</div>
@script
<script>
    $wire.on('messages-loaded', () => {
        const scrollableContent = $('.message-container');
        setTimeout(function()
        {
            scrollableContent.scrollTop(scrollableContent[0].scrollHeight);
        }, 10)
    });
</script>
@endscript
