<div class="card rounded-4 chat-container{{ $size }} overflow-hidden">
    <div class="card-body h-100">
        <div class="row h-100">
            <div class="col-md-6 col-lg-5 col-xl-4 mb-4 mb-md-0 p-3 h-100 d-flex flex-column">
                <div class="input-group mb-3">
                    <label for="session-select"
                           class="input-group-text">{{ trans_choice('common.child',$children->count()) }}</label>
                    <select id="student-select" wire:model="selectedStudentId" class="form-select"
                            wire:change="setStudent()">
                        @foreach($children as $child)
                            <option value="{{ $child->id }}">{{ $child->person->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="recipient-container flex-grow-1 overflow-auto">
                    <ul class="list-group list-group-flush">
                        @foreach($sessions as $session)
                            <li
                                    class="rounded list-group-item list-group-item-action show-as-action p-2 border-bottom d-flex justify-content-between @if($selectedSession && $session->id == $selectedSession->id) active @endif"
                                    wire:click="setSession({{ $session->id }})"
                                    wire:key="{{ $session->id }}"
                            >
                                <div class="d-flex flex-row">
                                    <div>
                                        <img
                                                src="{{ $session->teachers()->first()->thumbnail_url }}"
                                                alt="{{ $session->teachers()->first()->name }}"
                                                class="d-flex align-self-center me-3 person-thumbnail rounded-circle">
                                    </div>
                                    <div class="pt-1">
                                        <p class="fw-bold mb-0">{{ $session->name }}</p>
                                        @isset($this->latestMessage[$session->id])
                                            <p class="small text-muted">{{ \Illuminate\Support\Str::limit($this->latestMessage[$session->id]->message, 30, '...')  }}</p>
                                        @endisset
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <p class="small text-muted mb-1">
                                        {{ isset($this->latestMessage[$session->id])?
                                                $this->latestMessage[$session->id]->created_at->diffForHumans(): "" }}
                                    </p>
                                    @if($this->unreadMessages[$session->id] > 0)
                                        <span class="badge bg-danger rounded-pill float-end">{{ $this->unreadMessages[$session->id] }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            @if($student && $selectedSession)
                <livewire:school.class-chat :session="$selectedSession" :student="$student"
                                            :key="$selectedSession->id"/>
            @endif
        </div>

    </div>
</div>

