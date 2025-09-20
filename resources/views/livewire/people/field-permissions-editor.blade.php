<div>
    <div class="row justify-content-center mb-3">
        <div class="input-group">
            <label for="role_id" class="input-group-text">{{ __('people.fields.permissions.select') }}</label>
            <select name="role_id" id="role_id" class="form-select" wire:model="role_id" wire:change="loadRole()">
                <option value="0">{{ __('people.fields.permissions.basic') }}</option>
                @foreach(\App\Models\Utilities\SchoolRoles::all() as $schoolRole)
                    @if(count($schoolRole->fields) > 0)
                        <option value="{{ $schoolRole->id }}">{{ $schoolRole->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    <div class="row justify-content-center">
        <table class="table table-striped table-hover table-sm align-middle">
            <thead>
            <tr>
                <th>{{ __('people.policies.view.fields') }}</th>
                <th class="text-center">
                    {{ __('people.policies.view.viewable.self') }}
                </th>
                <th class="text-center">
                    {{ __('people.policies.view.editable') }}
                </th>
                <th class="text-center">
                    {{ __('people.policies.view.viewable.employees') }}
                </th>
                <th class="text-center">
                    {{ __('people.policies.view.viewable.students') }}
                </th>
                <th class="text-center">
                    {{ __('people.policies.view.viewable.parents') }}
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($fields as $field)
                <tr>
                    <td>{{ $selectedRole? $selectedRole->fields[$field->field]->fieldName: __('people.profile.fields.' . $field->field) }}</td>
                    <td class="text-center">
                        <input
                                type="checkbox"
                                value="1"
                                class="form-check-input"
                                @if($field->by_self)
                                    checked
                                @endif
                                wire:click="toggleField({{ $field->id }},'by_self')"
                        />
                    </td>
                    <td class="text-center">
                        <input
                                type="checkbox"
                                value="1"
                                class="form-check-input"
                                @if($field->editable)
                                    checked
                                @endif
                                wire:click="toggleField({{ $field->id }},'editable')"
                        />
                    </td>
                    <td class="text-center">
                        <input
                                type="checkbox"
                                value="1"
                                class="form-check-input"
                                @if($field->by_employees)
                                    checked
                                @endif
                                wire:click="toggleField({{ $field->id }},'by_employees')"
                        />
                    </td>
                    <td class="text-center">
                        <input
                                type="checkbox"
                                value="1"
                                class="form-check-input"
                                @if($field->by_students)
                                    checked
                                @endif
                                wire:click="toggleField({{ $field->id }},'by_students')"
                        />
                    </td>
                    <td class="text-center">
                        <input
                                type="checkbox"
                                value="1"
                                class="form-check-input"
                                @if($field->by_parents)
                                    checked
                                @endif
                                wire:click="toggleField({{ $field->id }},'by_parents')"
                        />
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
