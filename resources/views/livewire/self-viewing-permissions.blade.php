<div>
    @foreach(\App\Models\CRUD\ViewableGroup::all() as $group)
        @if($unenforcedFields->where('group_id', $group->id)->count() == 0)
            @continue;
        @endif
        <h5 class="border-bottom">{{ $group->name }}</h5>
        <table class="table table-striped table-hover table-sm align-middle">
            <thead>
            <tr>
                <th>{{ __('people.policies.view.fields') }}</th>
                <th class="text-center">{{ __('people.policies.view.show.question') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($unenforcedFields->where('group_id', $group->id) as $field)
                <tr wire:key="{{ $field->id }}">
                    <td>
                        <label class="form-check-label" for="field-{{ $field->id }}">{{ $field->name }}</label>
                    </td>
                    <td class="text-center">
                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="field-{{ $field->id }}"
                            @if($prefs[$field->id]) checked @endif
                            wire:click="toggleField({{ $field->id }})"
                        />
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endforeach
</div>
