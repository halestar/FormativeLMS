<div class="container">
    <div class="card rounded-4">
        @if(count($roles) > 1)
            <div class="card-header d-flex justify-content-start align-items-center">
                <span class="fw-bold me-3">{{ __('subjects.school.message.view.as') }}</span>
                @foreach($roles as $role => $viewVar)
                    <div class="form-check form-check-inline">
                        <input
                                class="form-check-input"
                                type="radio" name="viewingAs" id="{{ $viewVar }}"
                                value="{{ $role }}"
                                wire:model="selectedRole"
                                wire:change="updateRole"
                        >
                        <label
                                class="form-check-label"
                                for="viewingAs{{ $viewVar }}"
                        >{{ $role }}</label>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="card-body h-100">
            <div class="row" style="max-height: 70vh; height: 70vh;">
                <div class="col-4 overflow-auto h-100 mh-100">
                    @if($viewingAsTeacher)
                        <div class="input-group my-3">
                            <label for="session-select"
                                   class="input-group-text">{{ trans_choice('subjects.class', $sessions->count()) }}</label>
                            <select id="session-select" wire:model="selectedSessionId" class="form-select" wire:change="setSession">
                                @foreach($sessions as $session)
                                    <option value="{{ $session->id }}">{{ $session->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($viewingAsParent)
                        <div class="input-group mb-3">
                            <label for="student-select"
                                   class="input-group-text">{{ trans_choice('common.child', $students->count()) }}</label>
                            <select id="student-select" wire:model="selectedStudentId" class="form-select"
                                    wire:change="setStudent">
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->person->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($viewingAsAdmin)
                        <div class="input-group mb-3">
                            <label for="session-select"
                                   class="input-group-text">{{ __('school.student.tracking.current') }}</label>
                            <select id="session-select" wire:model="selectedStudentId" class="form-select"
                                    wire:change="setStudent">
                                @foreach($students as $tracked)
                                    <option value="{{ $tracked->id }}">{{ $tracked->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    @foreach($conversations as $conversation)
                        <livewire:school.class-messages.class-conversation-entry
                            :session="$conversation['session']"
                            :student="$conversation['student']"
                            :key="$conversation['session']->id . '_' . $conversation['student']->id"
                            :selected="($selectedConversation['session']->id == $conversation['session']->id && $selectedConversation['student']->id == $conversation['student']->id)"
                        />
                    @endforeach
                </div>
                <div class="col-8 d-flex flex-column h-100 mh-100">
                    <livewire:school.class-messages.class-chat
                        :session="$selectedConversation['session']"
                        :student="$selectedConversation['student']"
                        classes="flex-grow-1"
                        style="height: calc(100% - 70px);"
                    />
                    <livewire:school.class-messages.message-sender
                        :session="$selectedConversation['session']"
                        :student="$selectedConversation['student']"
                    />
                </div>
            </div>
        </div>
    </div>
</div>
