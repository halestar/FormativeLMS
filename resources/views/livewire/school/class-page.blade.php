<div class="container">
    <div class="row profile-head-row">
        <div class="col-md-4">
            <div class="profile-img">
                <img
                    class="img-fluid img-thumbnail"
                    src="{{ $classSession->course->campus->img }}"
                    alt="{{ __('locations.campus.img') }}"
                />
            </div>
        </div>
        <div class="col-md-6">
            <div class="profile-head d-flex align-items-start flex-column h-100">
                <h5>
                    {{ $classSession->name }}
                </h5>
                <h6 class="d-flex flex-column w-100">
                    <div>
                        {{ trans_choice('subjects.class.teacher', $classSession->teachers()->count()) }}:
                        {{ $classSession->teachersString() }}
                    </div>
                    <div>
                        {{ __('subjects.class.schedule') }}:
                        {{ $classSession->scheduleString() }}
                    </div>
                    <livewire:school.top-announcement :announcement="$classSession->layout->getTopAnnouncement()"  />
                </h6>
                <ul class="nav nav-tabs mt-auto w-100" id="profile-tabs" role="tablist" @if($editing) wire:sortable="updateTabOrder" @endif>
                    @foreach($tabs->getTabs() as $tab)
                    <li class="nav-item d-flex justify-content-start align-items-center" wire:key="{{ $tab->getId() }}" @if($editing) wire:sortable.item="{{ $tab->getId() }}" @endif>
                        @if($editing)
                        <span class="me-2 border-start ms-2 ps-2 border-black" wire:sortable.handle>
                            <i class="fa-solid fa-grip-lines-vertical"></i>
                        </span>
                        @endif
                        <a
                            class="nav-link @if(($selectedTab && $selectedTab->getId() == $tab->getId()) || (!$selectedTab && $loop->first)) active @endif"
                            id="tab-{{ $tab->getId() }}"
                            href="#"
                            role="tab"
                            @if(($selectedTab && $selectedTab->getId() == $tab->getId()) || (!$selectedTab && $loop->first))
                                aria-selected="true"
                            @else
                                aria-selected="true"
                            @endif
                            wire:click="selectTab('{{ $tab->getId() }}')"
                        >{{ $tab->name }}</a>
                    </li>
                    @endforeach
                    @if($editing)
                    <li class="nav-item">
                        <button type="button" class="btn btn-outline-primary btm-sm" data-bs-toggle="modal" data-bs-target="#class-modal">
                            <i class="fas fa-plus me-2 pe-2 border-end"></i>Add Tab
                        </button>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="col-md-2">
            @can('manage', $classSession)
                @if($editing)
                    <button
                        type="button"
                        class="btn btn-danger profile-edit-btn"
                        wire:click="setEdit(false)"
                    >{{ __('people.profile.editing') }}</button>
                @else
                    <button
                        type="button"
                        class="btn btn-secondary profile-edit-btn"
                        wire:click="setEdit(true)"
                    >{{ __('subjects.class.edit') }}</button>
                @endif
            @endcan
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="profile-work">

            </div>
        </div>
        <div class="col-md-6">
            <div class="tab-content profile-tab" id="profile-tab-content">
                <div
                    class="tab-pane active"
                    role="tabpanel"
                >
                    @if($editing)
                        <div class="tab-editor mb-3">
                            <div class="input-group">
                                <label for="edit-tab-name" class="input-group-text">{{ __('subjects.school.tabs.name') }}</label>
                                <input type="text" id="edit-tab-name" wire:model="editTabName" class="form-control @error('editTabName') is-invalid @enderror" />
                                <button
                                    type="button"
                                    class="btn btn-success"
                                    wire:click="updateTab()"
                                    >{{ __('subjects.school.tabs.name.update') }}</button>
                                @if(!$selectedTab->isLocked())
                                <button
                                    type="button"
                                    class="btn btn-danger"
                                    wire:click="deleteTab('{{ $selectedTab->getId() }}')"
                                    wire:confirm="{{ __('subjects.school.tabs.delete.prompt') }}"
                                >{{ __('subjects.school.tabs.delete') }}</button>
                                @endif
                            </div>
                            <div class="input-group mt-2">
                                <label for="add-widget" class="input-group-text">{{ __('subjects.school.widgets.add') }}</label>
                                <select id="add-widget" class="form-select">
                                    @foreach(config('class_management.widgets') as $widget)
                                        <option value="{{ $widget }}">{{ $widget::getWidgetName() }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-success" wire:click="addWidget($('#add-widget').val())">{{ __('common.add') }}</button>
                            </div>
                        </div>
                    @endif
                    <div class="widgets-container">
                        @foreach($widgets as $widget)
                            <div class="row justify-content-start my-2 g-0" wire:key="{{ $widget->getId() }}">
                                @if($editing)
                                    <div class="col-1">
                                        <div class="border-black border border-end-0 p-2 text-center bg-light rounded-start">
                                            <div class="widget-handle fs-4">
                                                <i class="fa-solid fa-grip-lines-vertical"></i>
                                            </div>
                                            <button
                                                type="button"
                                                class="btn btn-danger"
                                                wire:click="deleteWidget('{{ $widget->getId() }}')"
                                            ><i class="fa fa-times"></i></button>
                                        </div>
                                    </div>
                                @endif
                                <div class="col @if($editing) p-2 border border-black rounded-end rounded-bottom @endif">
                                    <livewire:dynamic-component :is="$widget->getComponentName()" :key="$widget->getId()" :classWidget="$widget" :canManage="$canManage" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @teleport('body')
    <div class="modal fade" id="class-modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="input-group">
                        <label for="tabName" class="input-group-text">{{ __('subjects.school.tabs.name') }}</label>
                        <input type="text" class="form-control" id="tabName" wire:model="tabName" />
                        <button type="button" class="btn btn-success" wire:click="addTab()"><i class="fa fa-check"></i></button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endteleport
</div>
@script
<script>
    $wire.on('close-add-tabs', () =>
    {
        $('#class-modal').modal('hide')
    });
</script>
@endscript
