<div>
    <div class="mt-3 profile-head">
        <ul class="nav nav-tabs mt-auto">
            @foreach(\App\Models\CRUD\ViewableGroup::crudItems() as $profileSection)
                <li class="nav-item" wire:key="{{ $profileSection->id }}">
                    <a
                        class="nav-link @if($active_tab == $profileSection->id) active @endif"
                        href="#"
                        wire:click="changeTab({{ $profileSection->id }})"
                    >{{ $profileSection->crudName() }}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="tab-content self-profile-tab" id="self-profile-tab-content">
        @foreach(\App\Models\CRUD\ViewableGroup::crudItems() as $profileSection)
            <div
                class="tab-pane fade @if($active_tab == $profileSection->id) show  active @endif"
                id="tab-{{ $profileSection->crudKey() }}-pane"
                role="tabpanel"
                aria-labelledby="tab-{{ $profileSection->crudKey() }}"
                tabindex="0"
            >
                <table class="table table-striped table-hover table-sm align-middle">
                    <thead>
                        <tr>
                            <th></th>
                            <th colspan="2" class="text-center">
                                {{ __('people.policies.view.self') }}
                            </th>
                            <th colspan="2" class="text-center">
                                {{ __('people.policies.view.employee') }}
                            </th>
                            <th colspan="2" class="text-center">
                                {{ __('people.policies.view.student') }}
                            </th>
                            <th colspan="2" class="text-center">
                                {{ __('people.policies.view.parent') }}
                            </th>
                        </tr>
                        <tr>
                            <th>{{ __('people.policies.view.fields') }}</th>
                            <th class="text-center">
                                {{ __('people.policies.view.viewable') }}
                            </th>
                            <th class="text-center">
                                {{ __('people.policies.view.editable') }}
                            </th>
                            <th class="text-center">
                                {{ __('people.policies.view.enforce') }}
                            </th>
                            <th class="text-center">
                                {{ __('people.policies.view.viewable') }}
                            </th>
                            <th class="text-center">
                                {{ __('people.policies.view.enforce') }}
                            </th>
                            <th class="text-center">
                                {{ __('people.policies.view.viewable') }}
                            </th>
                            <th class="text-center">
                                {{ __('people.policies.view.enforce') }}
                            </th>
                            <th class="text-center">
                                {{ __('people.policies.view.viewable') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody wire:sortable="updateFieldOrder">
                    @foreach($profileSection->fields as $field)
                        @php
                            $policyField = $viewPolicy->fields->where('id', $field->id)->first();
                            if(!$policyField && $viewPolicy->isBasePolicy())
                                {
                                    //add this field to the policy, since it's required
                                    $viewPolicy->fields()->attach($field->id);
                                }
                        @endphp
                        <tr wire:key="{{ $field->id }}" wire:sortable.item="{{ $field->id }}">
                            <td>
                                <span wire:sortable.handle class="show-as-action me-2"><i class="fa-solid fa-grip-lines-vertical"></i></span>
                                {{ $field->name }}
                            </td>
                            <td class="text-center">
                                <input
                                    type="checkbox"
                                    value="1"
                                    class="form-check-input"
                                    @if(!$policyField)
                                        disabled
                                    @elseif($policyField->permissions->self_viewable)
                                        checked
                                    @endif
                                    wire:click="toggleField({{ $field->id }},'self_viewable')"
                                />
                            </td>
                            <td class="text-center border-end">
                                <input
                                    type="checkbox"
                                    value="1"
                                    class="form-check-input"
                                    @if(!$policyField)
                                        disabled
                                    @elseif($policyField->permissions->editable)
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
                                    @if(!$policyField)
                                        disabled
                                    @elseif($policyField->permissions->employee_enforce)
                                        checked
                                    @endif
                                    wire:click="toggleField({{ $field->id }},'employee_enforce')"
                                />
                            </td>
                            <td class="text-center border-end">
                                <input
                                    type="checkbox"
                                    value="1"
                                    class="form-check-input"
                                    @if(!$policyField || !$policyField->permissions->employee_enforce)
                                        disabled
                                    @elseif($policyField->permissions->employee_viewable)
                                        checked
                                    @endif
                                    wire:click="toggleField({{ $field->id }},'employee_viewable')"
                                />
                            </td>
                            <td class="text-center">
                                <input
                                    type="checkbox"
                                    value="1"
                                    class="form-check-input"
                                    @if(!$policyField)
                                        disabled
                                    @elseif($policyField->permissions->student_enforce)
                                        checked
                                    @endif
                                    wire:click="toggleField({{ $field->id }},'student_enforce')"
                                />
                            </td>
                            <td class="text-center border-end">
                                <input
                                    type="checkbox"
                                    value="1"
                                    class="form-check-input"
                                    @if(!$policyField || !$policyField->permissions->student_enforce)
                                        disabled
                                    @elseif($policyField->permissions->student_viewable)
                                        checked
                                    @endif
                                    wire:click="toggleField({{ $field->id }},'student_viewable')"
                                />
                            </td>
                            <td class="text-center">
                                <input
                                    type="checkbox"
                                    value="1"
                                    class="form-check-input"
                                    @if(!$policyField)
                                        disabled
                                    @elseif($policyField->permissions->parent_enforce)
                                        checked
                                    @endif
                                    wire:click="toggleField({{ $field->id }},'parent_enforce')"
                                />
                            </td>
                            <td class="text-center">
                                <input
                                    type="checkbox"
                                    value="1"
                                    class="form-check-input"
                                    @if(!$policyField || !$policyField->permissions->parent_enforce)
                                        disabled
                                    @elseif($policyField->permissions->parent_viewable)
                                        checked
                                    @endif
                                    wire:click="toggleField({{ $field->id }},'parent_viewable')"
                                />
                            </td>
                            @if(!$policyField)
                                <td class="table-active text-center">
                                    <button
                                        type="button"
                                        class="btn btn-outline-primary btn-sm"
                                        wire:click="attachField({{ $field->id }})"
                                    >{{ strtolower(__('common.enable')) }}</button>
                                </td>
                            @elseif(!$viewPolicy->isBasePolicy())
                                <td class="table-active text-center">
                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        wire:click="dettachField({{ $field->id }})"
                                    >{{ strtolower(__('common.disable')) }}</button>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
</div>
