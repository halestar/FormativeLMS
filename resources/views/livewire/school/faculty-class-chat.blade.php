<div class="card rounded-4 chat-container{{ $size }} overflow-hidden">
    <div class="card-body h-100">
        <div class="row h-100">
            <div class="col-md-6 col-lg-5 col-xl-4 mb-4 mb-md-0 p-3 h-100 d-flex flex-column">
                <div class="input-group mb-3">
                    <label for="session-select" class="input-group-text">{{ trans_choice('subjects.class',$session->count()) }}</label>
                    <select id="session-select" wire:model="selectedSessionId" class="form-select" wire:change="setSession()">
                        @foreach($sessions as $classTaught)
                            <option value="{{ $classTaught->id }}">{{ $classTaught->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="recipient-container flex-grow-1 overflow-auto">
                    <ul class="list-group list-group-flush">
                        @foreach($students as $student)
                            <li
                                class="rounded list-group-item list-group-item-action show-as-action p-2 border-bottom d-flex justify-content-between @if($selectedStudent && $student->id == $selectedStudent->id) active @endif"
                                wire:click="setStudent({{ $student->id }})"
                                wire:key="{{ $student->id }}"
                            >
                                <div class="d-flex flex-row">
                                    <div>
                                        <img
                                            src="{{ $student->person->thumbnail_url }}"
                                            alt="{{ $student->person->name }}" class="d-flex align-self-center me-3 person-thumbnail rounded-circle">
                                    </div>
                                    <div class="pt-1">
                                        <p class="fw-bold mb-0">{{ $student->person->name }}</p>
                                        @isset($this->latestMessage[$student->id])
                                            <p class="small text-muted">{{ \Illuminate\Support\Str::limit($this->latestMessage[$student->id]->message, 30, '...')  }}</p>
                                        @endisset
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <p class="small text-muted mb-1">
                                        {{ isset($this->latestMessage[$student->id])?
                                                $this->latestMessage[$student->id]->created_at->diffForHumans(): "" }}
                                    </p>
                                    @if($this->unreadMessages[$student->id] > 0)
                                        <span class="badge bg-danger rounded-pill float-end">{{ $this->unreadMessages[$student->id] }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @if($selectedStudent && $session)
                <livewire:school.class-chat :session="$session" :student="$selectedStudent" :key="$selectedStudent->id" />
            @endif
        </div>
    </div>
</div>

