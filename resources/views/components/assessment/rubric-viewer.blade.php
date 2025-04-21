<div>
    @use(App\Casts\Rubric)
    <table class="table table-bordered" style="table-layout: fixed;">
        <thead>
        <tr>
            <th scope="col" class="p-0">
                <div class="text-bg-dark p-1 text-end"> {{ __('subjects.skills.rubric.builder.score') }} </div>
                <div class="p-1">{{ __('subjects.skills.rubric.builder.criteria') }}</div>
            </th>
            @foreach($rubric->points as $score)
                <th scope="col" class="text-center table-dark align-middle position-relative">
                    <strong>
                        {{ $score . " " . __('subjects.skills.rubric.builder.score.points') }}
                    </strong>
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($rubric->criteria as $crit)
            <tr>
                <th scope="row" class="align-top position-relative">
                    <div class="d-flex h-100">
                        <div class="flex-grow-1">{{ $crit }}</div>
                    </div>
                </th>
                @foreach($rubric->descriptions[$loop->index] as $description)
                    <td>
                        <div class="d-flex">
                            <div class="flex-grow-1">{!! nl2br($description) !!}</div>
                        </div>
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
