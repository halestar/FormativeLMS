<div
        class="rounded list-group-item list-group-item-light list-group-item-action show-as-action p-1 border-bottom d-flex justify-content-between align-items-center"
        x-data="{ selected: $wire.entangle('selected'), numUnreadMessages: $wire.entangle('numUnreadMessages') }"
        wire:click="selectConversation"
        :class="selected && 'active'"
        x-on:class-messages-change-conversation.window="
            if(event.detail.session != '{{ $session->id }}' || event.detail.student != '{{ $student->id }}')
                selected = false
        "

>
    <div class="avatar-list-container">
        <div class="position-relative">
            @if($session->viewingAs(\App\Enums\ClassViewer::FACULTY))
                <img
                        src="{{ $student->person->portrait_url->thumbUrl() }}"
                        alt="{{ $student->person->name }}"
                        class="d-flex align-self-center me-3 avatar-img-xl rounded-circle avatar-list-item">
            @else
                @foreach($session->teachers as $teacher)
                    <img
                            src="{{ $teacher->portrait_url->thumbUrl() }}"
                            alt="{{ $teacher->name }}"
                            class="d-flex align-self-center me-3 rounded-circle avatar-list-item avatar-img-xl"
                    />
                @endforeach
            @endif
            <span
                class="position-absolute top-0 end-0 badge rounded-pill text-bg-danger"
                x-cloak
                x-show="numUnreadMessages > 0"
                x-text="numUnreadMessages"
            ></span>
        </div>
    </div>
    <div class="ms-3 flex-grow-1">
        <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">
                    {{ $session->viewingAs(\App\Enums\ClassViewer::FACULTY) ? $student->person->name: $session->name_with_schedule }}
                </span>
            @if($latestMessage)
                <span class="small text-muted">
                        {{ $latestMessage->created_at->diffForHumans()}}
                </span>
            @endif
        </div>
        @if($latestMessage)
            <span class="small text-muted">{{ \Illuminate\Support\Str::limit($latestMessage->message, 30, '...')  }}</span>
        @endisset
    </div>
</div>
