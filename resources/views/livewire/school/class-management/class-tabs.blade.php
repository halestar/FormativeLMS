<div class="{{ $classes }}" style="{{ $style }}">
    @inject('componentRegistry', 'Livewire\Mechanisms\ComponentRegistry')
    <ul
        class="nav nav-tabs mt-auto w-100" id="profile-tabs"
        role="tablist"
        @if($editing) wire:sortable="updateTabOrder" @endif
    >
        @foreach($tabs as $tab)
            <li class="nav-item d-flex justify-content-start align-items-center"
                wire:key="{{ $tab->getId() }}"
                @if($editing) wire:sortable.item="{{ $tab->getId() }}" @endif
            >
                @if($editing)
                    <span class="me-2 border-start ms-2 ps-2 border-black" wire:sortable.handle>
                        <i class="fa-solid fa-grip-lines-vertical"></i>
                    </span>
                @endif
                <button
                        class="nav-link @if(($selectedTab && $selectedTab->getId() == $tab->getId()) || (!$selectedTab && $loop->first)) active @endif"
                        id="tab-{{ $tab->getId() }}"
                        href="#"
                        role="tab"
                        @if(($selectedTab && $selectedTab->getId() == $tab->getId()) || (!$selectedTab && $loop->first))
                            aria-selected="true"
                        @else
                            aria-selected="false"
                        @endif
                        wire:click="selectTab('{{ $tab->getId() }}')"
                >{{ $tab->name }}</button>
            </li>
        @endforeach
        @if($editing)
            <li class="nav-item">
                <button type="button" class="btn btn-outline-primary btm-sm" wire:click="addTab">
                    <i class="fas fa-plus me-2 pe-2 border-end"></i>{{ __('subjects.school.tabs.add') }}
                </button>
            </li>
        @endif
    </ul>
    @if($editing)
    <div class="m-0 p-3 border border-top-0 shadow-sm-inset rounded-bottom-0 text-bg-light">
        <div class="input-group">
            <label for="edit-tab-name"
                   class="input-group-text">{{ __('subjects.school.tabs.name') }}</label>
            <input type="text" id="edit-tab-name" wire:model="tabName"
                   class="form-control @error('editTabName') is-invalid @enderror"/>
            <button
                    type="button"
                    class="btn btn-success"
                    wire:click="updateTab()"
            >{{ __('subjects.school.tabs.name.update') }}</button>
            @if($selectedTab->canDelete())
                <button
                        type="button"
                        class="btn btn-danger"
                        wire:click="deleteTab"
                        wire:confirm="{{ __('subjects.school.tabs.delete.prompt') }}"
                >{{ __('subjects.school.tabs.delete') }}</button>
            @endif
        </div>
        @if($selectedTab->canRemoveWidget())
            <div class="input-group mt-2">
                <label for="add-widget"
                       class="input-group-text">{{ __('subjects.school.widgets.set') }}</label>
                <select id="add-widget" class="form-select" wire:change="setWidget($event.target.value)">
                    <option value="">{{ __('subjects.school.widgets.select') }}</option>
                    @foreach($availableWidgets as $widgetClass => $widgetName)
                        <option value="{{ $widgetClass }}" @selected($widgetClass == $selectedTab->widget)>{{ $widgetName }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>
    @endif
    <div class="tab-content mt-3">
        @foreach($tabs as $tab)
            <div
                class="tab-pane fade @if($selectedTab->getId() == $tab->getId()) show active @endif"
                role="tabpanel"
                tabindex="{{ $loop->index }}"
                wire:key="{{ $tab->getId() }}"
            >
                <div class="widget-container">
                    @if($tab->widget)
                    <livewire:dynamic-component :is="$componentRegistry->getName($tab->widget)" :key="$tab->widget" :session="$classSession" />
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
