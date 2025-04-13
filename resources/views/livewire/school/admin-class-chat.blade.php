<div class="card rounded-4 chat-container{{ $size }} overflow-hidden">
    <div class="card-body h-100">
        <div class="row h-100">
            <div class="col-md-6 col-lg-5 col-xl-4 mb-4 mb-md-0 p-3 h-100 d-flex flex-column">
                <div class="input-group mb-3">
                    <label for="session-select" class="input-group-text">{{ __('school.student.tracking.current') }}</label>
                    <select id="session-select" wire:model="selectedStudentId" class="form-select" wire:change="setStudent()">
                        @foreach($students as $tracked)
                            <option value="{{ $tracked->id }}">{{ $tracked->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="recipient-container flex-grow-1 overflow-auto">
                    <ul class="list-group list-group-flush">
                        @foreach($sessions as $rosterSession)
                            <li
                                class="rounded list-group-item list-group-item-action show-as-action p-2 border-bottom d-flex justify-content-between @if($session && $session->id == $rosterSession->id) active @endif"
                                wire:click="setSession({{ $rosterSession->id }})"
                                wire:key="{{ $rosterSession->id }}"
                            >
                                <div class="d-flex flex-row">
                                    <div class="avatar-list-container">
                                        @if($rosterSession->teachers->count() > 1)
                                            @foreach($rosterSession->teachers as $teacher)
                                                <img
                                                    src="{{ $teacher->thumbnail_url }}"
                                                    alt="{{ $teacher->name }}"
                                                    class="d-flex align-self-center me-3 person-thumbnail rounded-circle avatar-list-item avatar-img-normal"
                                                />
                                            @endforeach
                                        @else
                                            <img
                                                src="{{ $rosterSession->teachers->first()->thumbnail_url }}"
                                                alt="{{ $rosterSession->teachers->first()->name }}"
                                                class="d-flex align-self-center me-3 person-thumbnail rounded-circle avatar-list-item avatar-img-normal"
                                            />
                                        @endif
                                    </div>
                                    <div class="pt-1">
                                        <p class="fw-bold mb-0 ">{{ $rosterSession->name }}</p>
                                        @isset($this->latestMessage[$rosterSession->id])
                                            <p class="small text-muted">{{ \Illuminate\Support\Str::limit($this->latestMessage[$rosterSession->id]->message, 30, '...')  }}</p>
                                        @endisset
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <p class="small text-muted mb-1">
                                        {{ isset($this->latestMessage[$rosterSession->id])?
                                                $this->latestMessage[$rosterSession->id]->created_at->diffForHumans(): "" }}
                                    </p>
                                    @if($this->unreadMessages[$rosterSession->id] > 0)
                                        <span class="badge bg-danger rounded-pill float-end">{{ $this->unreadMessages[$rosterSession->id] }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @if($selectedStudent && $session)
                <livewire:school.class-chat :session="$session" :student="$selectedStudent" :key="$session->id" />
            @endif
        </div>
    </div>
</div>

