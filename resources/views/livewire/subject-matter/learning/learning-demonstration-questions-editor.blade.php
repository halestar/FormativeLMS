<div class="card">
    @use('App\Classes\Learning\DemonstrationQuestion')
    <div class="card-header d-flex justify-content-between align-items-center">
        <div class="card-title">{{ __('learning.demonstrations.questions') }}</div>
        @if($canUseAI)
            <div class="d-flex justify-content-center">
                <livewire:ai.run-model-prompt
                        :model="$ld" property="questions"
                        classes="m-2 p-2 border rounded text-bg-light"
                        btn-classes="btn btn-sm btn-light border"
                        teleport-to="#questions-ai-results"
                />
            </div>
        @endif
        <button
                type="button"
                class="btn btn-sm btn-primary"
                wire:click="addQuestion"
        >{{ __('learning.demonstrations.questions.add') }}</button>
    </div>
    <div class="card-body">
        <div id="questions-ai-results"></div>
        <ul class="list-group list-group-flush">
            @foreach($questions as $question)
                <li class="list-group-item d-flex justify-content-between align-items-center"
                    wire:key="question-{{$loop->index}}">
                    <div class="me-3 flex-grow-1">
                        <div class="input-group mb-3">
                            <span class="input-group-text">{{ __('learning.demonstrations.questions.question') }}:</span>
                            <textarea
                                    type="text"
                                    class="form-control"
                                    wire:model="questions.{{$loop->index}}.question"
                                    row="3"
                            >{!! $question['question'] !!}</textarea>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text">{{ __('learning.demonstrations.questions.type') }}:</span>
                            <select
                                    class="form-select"
                                    wire:model.live="questions.{{$loop->index}}.type"
                                    id="question-type-{{ $loop->index }}"
                            >
                                @foreach(DemonstrationQuestion::typeOptions() as $value => $label)
                                    <option value="{{ $value }}" @selected($question['type'] == $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($question['type'] == DemonstrationQuestion::TYPE_MULTIPLE ||
                            $question['type'] == DemonstrationQuestion::TYPE_CHOICE)
                            <div>
                                <h5 class="border-bottom mb-2">{{ __('learning.demonstrations.questions.options') }}</h5>
                                <div class="d-flex flex-wrap gap-2 p-3 my-3">
                                    @foreach($question['options'] as $option)
                                        <span class="badge bg-info"
                                              wire:key="option-{{ $loop->parent->index }}-{{ $loop->index }}">
                                            {{ $option }}
                                            <span
                                                    class="border-start border-2 border-info ps-2 text-danger"
                                                    wire:click="removeAnswer({{$loop->parent->index}}, {{ $loop->index }})"
                                            >
                                                <i class="fa-solid fa-times"></i>
                                            </span>
                                        </span>
                                    @endforeach
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text">{{ __('learning.demonstrations.questions.options.add') }}</span>
                                    <input
                                            type="text"
                                            class="form-control"
                                            id="add-answer-{{ $loop->index }}"
                                            wire:change="addAnswer({{$loop->index}}, $event.target.value);$event.target.value = ''"
                                    />
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="ps-2 border-start">
                        <button
                                type="button"
                                class="btn btn-sm btn-danger"
                                wire:click="removeQuestion({{$loop->index}})"
                        ><i class="fa-solid fa-times"></i></button>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>