<div>
    @use(App\Casts\Learning\Rubric)
    <div class="row mb-4">
        <div class="col-lg-4">
            <div class="align-self-start d-flex flex-column">
                <div class="border rounded border-secondary p-3 text-bg-secondary mb-2">
                    <div class="input-group mb-2">
                        <span class="input-group-text">{{ __('subjects.skills.rubric.builder.score.add') }}</span>
                        <input
                                class="form-control text-center @error('newScore') is-invalid @enderror text-nowrap"
                                type="number"
                                min="0"
                                step="1"
                                wire:model.blur="newScore"
                        />
                        <span class="input-group-text">{{ __('subjects.skills.rubric.builder.score.points') }}</span>
                        <button
                                type="button"
                                class="btn btn-primary text-nowrap"
                                wire:click="addScore()"
                        >{{ __('common.add') }}</button>
                        <x-utilities.error-display key="newScore">{{ $errors->first('newScore') }}</x-utilities.error-display>
                    </div>
                    <div class="row px-3 mb-2">
                        <button
                                type="button"
                                class="col btn btn-primary text-nowrap"
                                wire:click="addCriteria()"
                        >{{ __('subjects.skills.rubric.builder.criteria.add') }}</button>
                    </div>
                    <div class="row px-3 mb-2">
                        <button
                                type="button"
                                class="col btn btn-danger text-nowrap"
                                wire:click="clearRubric()"
                                wire:confirm="{{ __('subjects.skills.rubric.builder.clear.confirm') }}"
                        >{{ __('subjects.skills.rubric.builder.clear') }}</button>
                    </div>

                    @if($canUseAI)
                        <div class="row px-3 mb-2">
                            <livewire:ai.run-model-prompt
                                    :model="$skill" property="rubric"
                                    classes="m-2 p-2 border rounded text-bg-light"
                                    btn-classes="w-100 btn btn-light border text-nowrap"
                                    teleport-to="#skills-ai-results"
                            />
                        </div>
                    @endif
                </div>
                @if($saved)
                    <div class="alert alert-success">
                        {{ __('subjects.skills.rubric.builder.saved') }}
                    </div>
                @else
                    <div class="alert alert-warning fw-bold">
                        {{ __('subjects.skills.rubric.builder.saved.no') }}
                        <button
                                type="button"
                                class="btn btn-success btn-sm ms-2"
                                wire:click="save()"
                        >{{ __('subjects.skills.rubric.builder.save') }}</button>
                        <button
                                type="button"
                                class="btn btn-danger btn-sm ms-2"
                                wire:click="discardChanges()"
                                wire:confirm="{{ __('subjects.skills.rubric.builder.saved.discard.confirm') }}"
                        >{{ __('subjects.skills.rubric.builder.saved.discard') }}</button>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-8">
            <div class="alert alert-info ms-2">
                {!! $skill->description !!}
            </div>
        </div>
    </div>
    <div id="skills-ai-results"></div>
    <div class="table-responsive-lg p-5">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th scope="col" class="p-0">
                    <div class="text-bg-dark p-1 text-end"> {{ __('subjects.skills.rubric.builder.score') }} </div>
                    <div class="p-1">{{ __('subjects.skills.rubric.builder.criteria') }}</div>
                </th>
                @foreach($rubric->points as $score)
                    <th scope="col" class="text-center table-dark align-middle position-relative"
                        wire:key="{{ $loop->index }}">
                        <div
                                class="position-absolute top-0 start-50 translate-middle-x d-flex"
                                style="margin-top: -25px; width: 50px; height: 25px;"
                        >
                            <div
                                    class="text-bg-primary border-top border-end border-bottom border-primary rounded-top show-as-action"
                                    style="width: 25px; height: 25px;"
                                    title="{{ __('common.edit') }}"
                                    wire:click="setUpdateScore({{ $loop->index }})"
                            >
                                <i class="fa-solid fa-edit fs-6 pt-1"></i>
                            </div>
                            <div
                                    class="text-bg-danger border-top border-end border-bottom border-danger rounded-top show-as-action"
                                    style="width: 25px; height: 25px;"
                                    wire:click="removeScore({{ $score }})"
                                    wire:confirm="{{ __('subjects.skills.rubric.builder.score.remove.confirm') }}"
                                    title="{{ __('subjects.skills.rubric.builder.score.remove') }}"
                            >
                                <i class="fa-solid fa-times fs-5 pt-1"></i>
                            </div>
                        </div>
                        @if($updating && $updateColumn == $loop->index && $updateType == "points")
                            <div class="input-group">
                                <input
                                        class="form-control"
                                        type="number"
                                        min="0.1"
                                        step="0.1"
                                        wire:model="updateScoreValue"
                                />
                                <button
                                        type="button"
                                        class="btn btn-success btn-sm"
                                        wire:click="updateScore()"
                                ><i class="fa-solid fa-check"></i></button>
                                <button
                                        type="button"
                                        class="btn btn-secondary btn-sm"
                                        wire:click="clearEdit()"
                                ><i class="fa-solid fa-times"></i></button>
                            </div>
                        @else
                            <strong>
                                {{ $score . " " . __('subjects.skills.rubric.builder.score.points') }}
                            </strong>
                        @endif
                    </th>
                @endforeach
            </tr>
            </thead>
            <tbody wire:sortable="updateCriteriaOrder">
            @foreach($rubric->criteria as $crit)
                <tr wire:key="criteria-{{ $loop->index }}" wire:sortable.item="{{ $loop->index }}">
                    <th scope="row" class="align-top position-relative">
                        <div
                                class="position-absolute top-50 start-0 translate-middle"
                                style="width: 25px; height: 50px; margin-left: -14px;"
                        >
                            <div
                                    class="border-top border-start border-bottom border-secondary rounded-start show-as-grab text-bg-secondary d-flex align-items-center justify-content-start"
                                    style="width: 25px; height: 25px;"
                                    role="button"
                                    wire:sortable.handle
                            >
                                <i class="fa-solid fa-grip-lines-vertical fs-4 mx-auto"></i>
                            </div>
                            <div
                                    class="border-top border-start border-bottom border-danger rounded-start show-as-action text-bg-danger d-flex align-items-center justify-content-start"
                                    style="width: 25px; height: 25px;"
                                    role="button"
                                    wire:confirm="{{ __('subjects.skills.rubric.builder.criteria.remove.confirm') }}"
                                    wire:click="removeCriteria({{ $loop->index }})"
                            >
                                <i class="fa-solid fa-times fs-4 mx-auto"></i>
                            </div>
                        </div>
                        @if($updating && $updateType == "criteria" && $updateRow == $loop->index)
                            <div class="d-flex justify-content-center flex-column">
                            <textarea
                                    class="form-control flex-grow-1"
                                    wire:model="updateValue"
                                    style="height: 200px;"
                            ></textarea>
                                <div class="d-flex justify-content-center mt-2">
                                    <button
                                            type="button"
                                            class="btn btn-success mx-2"
                                            wire:click="updateCriteria()"
                                    ><i class="fa-solid fa-check"></i></button>
                                    <button
                                            type="button"
                                            class="btn btn-secondary mx-2"
                                            wire:click="clearEdit()"
                                    ><i class="fa-solid fa-times"></i></button>
                                </div>
                            </div>
                        @else
                            <div class="d-flex h-100">
                                <div class="flex-grow-1">{{ $crit }}</div>
                                <button
                                        type="button"
                                        class="btn btn-sm btn-primary ms-1 align-self-start"
                                        wire:click="setUpdateCriteria({{ $loop->index }})"
                                ><i class="fa-solid fa-edit"></i></button>
                            </div>
                        @endif
                    </th>
                    @foreach($rubric->descriptions[$loop->index] as $description)
                        <td wire:key="{{ $loop->parent->index . " " . $loop->index }}">
                            @if($updating &&
                                $updateType == "descriptions" &&
                                $updateRow == $loop->parent->index &&
                                $updateColumn == $loop->index)

                                <div class="d-flex justify-content-center flex-column">
                                    <textarea
                                            class="form-control flex-grow-1"
                                            wire:model="updateValue"
                                            style="height: 200px;"
                                    ></textarea>
                                    <div class="d-flex justify-content-center mt-2">
                                        <button
                                                type="button"
                                                class="btn btn-success mx-2"
                                                wire:click="updateDescription()"
                                        ><i class="fa-solid fa-check"></i></button>
                                        <button
                                                type="button"
                                                class="btn btn-secondary mx-2"
                                                wire:click="clearEdit()"
                                        ><i class="fa-solid fa-times"></i></button>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex">
                                    <div class="flex-grow-1">{!! nl2br($description) !!}</div>
                                    <button
                                            type="button"
                                            class="btn btn-sm btn-primary ms-1 align-self-start"
                                            wire:click="setUpdateDescription({{ $loop->parent->index }}, {{ $loop->index }})"
                                    ><i class="fa-solid fa-edit"></i></button>
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
