<div class="table-responsive">
    @use(App\Casts\Learning\Rubric)
    <table class="table table-bordered">
        <thead>
        <tr>
            <th scope="col" class="p-0">
                <div class="text-bg-dark p-1 text-end"> {{ __('subjects.skills.rubric.builder.score') }} </div>
                <div class="p-1">{{ __('subjects.skills.rubric.builder.criteria') }}</div>
            </th>
            @foreach($rubric->points as $score)
                <th scope="col" class="text-center table-dark align-middle position-relative" wire:key="points-{{ $score }}">
                    <strong>
                        {{ $score . " " . __('subjects.skills.rubric.builder.score.points') }}
                    </strong>
                </th>
            @endforeach
            <th scope="col" class="text-center table-dark align-middle position-relative">
                <button type="button" class="btn btn-info btn-sm" wire:click="resetRubric">{{ __('subjects.skills.rubric.revert') }}</button>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($rubric->criteria as $crit)
            <tr wire:key="rubric-criteria-{{ $loop->index }}">
                <th scope="row" class="align-top position-relative">
                    {{ $crit }}
                </th>
                @foreach($rubric->descriptions[$loop->index] as $description)
                    <td wire:key="rubric-description-{{ $loop->parent->index }}-{{ $loop->index }}">
                        {!! nl2br($description) !!}
                    </td>
                @endforeach
                <td class="align-middle">
                    @if($rubric->numCriteria() > 1)
                    <button
                        type="button"
                        class="btn btn-danger btn-sm"
                        wire:click="removeCriteria({{ $loop->index }})"
                    >{{ __('subjects.skills.rubric.builder.criteria.remove') }}</button>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
