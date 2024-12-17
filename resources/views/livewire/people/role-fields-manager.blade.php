<div>
    <div class="row justify-content-center mb-3">
        <div class="input-group">
            <label for="role_id" class="input-group-text">{{ __('people.roles.field.select') }}</label>
            <select name="role_id" id="role_id" class="form-select" wire:model="role_id" wire:change="loadRole()">
                <option>{{ __('people.roles.select') }}</option>
                @foreach (\App\Models\Utilities\SchoolRoles::all() as $schoolRole)
                    <option value="{{ $schoolRole->id }}">{{ $schoolRole->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    @if($role_id)
        <div class="row">
            <div class="col-md-4">
                <h3>{{ __('people.roles.field.create') }}</h3>
                <div class="mb-3">
                    <label for="fieldName" class="form-label">{{ __('people.roles.field.name') }}</label>
                    <input
                        type="text"
                        name="fieldName"
                        id="fieldName"
                        wire:model="fieldName"
                        class="form-control @error('fieldName') is-invalid @enderror"
                        wire:change="updatePreview()"
                    />
                    <x-error-display key="fieldName">{{ $errors->first('fieldName') }}</x-error-display>
                </div>
                <div class="mb-3">
                    <label for="fieldType" class="form-label">{{ __('people.roles.field.name') }}</label>
                    <select
                        type="text"
                        name="fieldType"
                        id="fieldType"
                        wire:model="fieldType"
                        class="form-select @error('fieldType') is-invalid @enderror"
                        wire:change="updatePreview()"
                    >
                        @foreach(\App\Classes\RoleField::FIELDS as $id => $name)
                            <option value="{{ $id }}">{{ __($name) }}</option>
                        @endforeach
                    </select>
                    <x-error-display key="fieldType">{{ $errors->first('fieldType') }}</x-error-display>
                </div>
                <div class="mb-3">
                    <label for="fieldHelp" class="form-label">{{ __('people.roles.field.help') }}</label>
                    <textarea type="text" name="fieldHelp" id="fieldHelp" wire:model="fieldHelp" class="form-control" wire:change="updatePreview()"></textarea>
                </div>
                @if($fieldType == \App\Classes\RoleField::CHECKBOX || $fieldType == \App\Classes\RoleField::SELECT ||
                    $fieldType == \App\Classes\RoleField::RADIO)

                    @if($fieldType == \App\Classes\RoleField::CHECKBOX)
                        <div class="mb-3">
                            <label class="form-label">{{ __('common.default') }}</label>
                            <br />
                            @foreach($fieldOptions as $option)
                                <div class="form-check form-check-inline">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="default_{{ $loop->iteration }}"
                                        wire:model="fieldPlaceholder"
                                        value="{{ $option }}"
                                        wire:click="updatePreview()"
                                    />
                                    <label class="form-check-label" for="default_{{ $loop->iteration }}">{{ $option }}</label>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mb-3">
                            <label for="fieldPlaceholder" class="form-label">{{ __('common.default') }}</label>
                            <select id="fieldPlaceholder" wire:model="fieldPlaceholder" class="form-select" wire:change="updatePreview()">
                                <option>{{ __('common.default.no') }}</option>
                                @foreach($fieldOptions as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="fieldOptions" class="form-label">{{ __('people.roles.field.options') }}</label>
                        <div class="input-group">
                            <label for="newOption" class="input-group-text">{{ __('common.option') }}:</label>
                            <input type="text" id="newOption" class="form-control"
                                   wire:keyup.enter="addOption($('#newOption').val());$('#newOption').val('')"/>
                            <button type="button" class="btn btn-primary"
                                    wire:click="addOption($('#newOption').val());$('#newOption').val('')">{{ __('common.add') }}
                            </button>
                        </div>
                        <select
                            id="fieldOptions"
                            name="fieldOptions"
                            size="5"
                            class="form-select @error('fieldOptions') is-invalid @enderror"
                            aria-describedby="optionsHelp"
                            wire:click="removeOption($event.target.value)"
                        >
                            @foreach($fieldOptions as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                        <div id="optionsHelp" class="form-text">
                            {{ __('people.roles.field.options.help') }}
                        </div>
                        <x-error-display key="fieldOptions">{{ $errors->first('fieldOptions') }}</x-error-display>
                    </div>
                @else
                    <div class="mb-3">
                        <label for="fieldPlaceholder" class="form-label">{{ __('people.roles.field.placeholder') }}</label>
                        <input type="text" name="fieldPlaceholder" id="fieldPlaceholder" wire:model="fieldPlaceholder" class="form-control" wire:change="updatePreview()" />
                    </div>
                @endif
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary col " wire:click="addField()">{{ __('people.roles.field.add') }}</button>
                </div>
            </div>
            <div class="col-md-4">
                <h3>{{ __('people.roles.field.preview') }}</h3>
                @if($fieldPreview)
                    {!! $fieldPreview->getHtml() !!}
                @endif
            </div>
            <div class="col-md-4">
                <h3>{{ __('people.roles.field.existing') }}</h3>
                @if($role)
                    <div class="input-group mb-3">
                        <label for="copy_id" class="input-group-text">{{ trans_choice('people.roles.field.copy', 2) }}</label>
                        <select name="copy_id" id="copy_id" class="form-select">
                            <option>{{ __('people.roles.select') }}</option>
                            @foreach (\App\Models\Utilities\SchoolRoles::where('id', '<>', $role_id)->get() as $schoolRole)
                                <option value="{{ $schoolRole->id }}">{{ $schoolRole->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-primary" wire:click="copyAllToRole($('#copy_id').val())">
                            {{ __('common.copy') }}</button>
                    </div>
                    <ul class="list-group">
                        @foreach($role->fields as $field)
                            <li class="list-group-item" wire:key="{{ $field->fieldName }}">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="field-container">
                                        {!! $field->getHTML() !!}
                                    </div>
                                    <button
                                        type="button"
                                        class="btn btn-danger"
                                        wire:confirm="Are you sure you wish to delete this form field?"
                                        wire:click="removeField('{{ $field->fieldId }}')"
                                    ><i class="fa-solid fa-times"></i></button>
                                </div>
                                <div class="input-group mb-3">
                                    <label for="copy_id_{{ $loop->iteration }}" class="input-group-text">{{ trans_choice('people.roles.field.copy', 1) }}</label>
                                    <select id="copy_id_{{ $loop->iteration }}" class="form-select">
                                        <option>Select a Role</option>
                                        @foreach (\App\Models\Utilities\SchoolRoles::where('id', '<>', $role_id)->get() as $schoolRole)
                                            <option value="{{ $schoolRole->id }}">{{ $schoolRole->name }}</option>
                                        @endforeach
                                    </select>
                                    <button
                                        type="button"
                                        class="btn btn-primary"
                                        wire:click="copyFieldToRole('{{ $field->fieldId }}', $('#copy_id_{{ $loop->iteration }}').val())"
                                    >{{ __('common.copy') }}</button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif
</div>
