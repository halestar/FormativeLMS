<div>
    @if($canManage)
        @if(!($editing || $adding))
            <div class="border rounded bg-light p-3 mb-3">
                <div class="row">
                    <div class="col-10">
                        <input
                                type="text"
                                id="class-links-title"
                                wire:model="classLinksTitle"
                                class="form-control"
                                placeholder="{{ __('subjects.school.widgets.class-links.title') }}"
                                wire:change="updateTitle()"
                        />
                    </div>
                    <button type="button" class="btn btn-primary col-2"
                            wire:click="setAdd()">{{ __('common.add') }}</button>
                </div>
            </div>
        @endif
        <div class="border rounded bg-light p-3 @if(!($editing || $adding)) d-none @endif">
            <div class="mb-3">
                <label for="link_text" class="form-label">{{ __('subjects.school.widgets.class-links.text') }}</label>
                <input type="text" id="link_text" class="form-control @error('linkText') is-invalid @enderror"
                       wire:model="linkText"/>
                <x-error-display key="linkText">{{ $errors->first('linkText') }}</x-error-display>
            </div>
            <div class="mb-3">
                <label for="link_url" class="form-label">{{ __('subjects.school.widgets.class-links.link') }}</label>
                <input type="text" id="link_url" class="form-control @error('linkUrl') is-invalid @enderror"
                       wire:model="linkUrl"/>
                <x-error-display key="linkUrl">{{ $errors->first('linkUrl') }}</x-error-display>
            </div>
            <div class="row">
                @if($adding)
                    <button type="button" class="btn btn-primary col"
                            wire:click="addLink()">{{ __('common.add') }}</button>
                @else
                    <button type="button" class="btn btn-primary col" wire:click="updateLink()">
                        {{ __('common.update') }}</button>
                @endif
                <button type="button" class="btn btn-secondary col" wire:click="clearLinkForm()">
                    {{ __('common.cancel') }}</button>
            </div>
            @if($adding && count($otherWidgets) > 0)
                <div class="mt-3 pt-3 border-top">
                    <h4>{{ __('subjects.school.widgets.class-announcements.post.also') }}</h4>
                    <div class="row row-cols-4">
                        @foreach($sessionWidgets as $session)
                            @foreach($session['widgets'] as $w)
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ow-{{ $w->getId() }}"
                                           value="{{ $w->getId() }}" wire:model="alsoPost"/>
                                    <label class="form-check-label" for="ow-{{ $w->getId() }}">{{ $w->title }}
                                        ({{ $session['session']->name }})</label>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @elseif($editing)
                <div class="mt-2 pt-2 border-top">
                    <div class="form-check fs-4">
                        <input
                                type="checkbox"
                                id="notify"
                                wire:model="notify"
                                class="form-check-input"
                        />
                        <label for="notify"
                               class="form-check-label">{{ __('subjects.school.widgets.class-announcements.notify') }}</label>
                    </div>
                </div>
            @endif
        </div>
        <ul class="list-group">
            @foreach($links as $link)
                <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                        wire:key="{{ $link['id'] }}"
                >
                    {{ $link['text'] }}
                    <div class="link-control">
                        <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                wire:click="setEdit('{{ $link['id'] }}')"
                        ><i class="fa fa-edit"></i></button>
                        <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                wire:click="deleteLink('{{ $link['id'] }}')"
                        ><i class="fa fa-times"></i></button>
                        <a
                                type="button"
                                class="btn btn-success btn-sm"
                                href="{{ $link['url'] }}"
                                target="_new"
                        ><i class="fa-solid fa-right-long"></i></a>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <h3 class="class-announcements-title">{{ $widget->title }}</h3>
        <div class="list-group">
            @foreach($links as $link)
                <a
                        class="list-group-item list-group-item-action"
                        wire:key="{{ $link['id'] }}"
                        href="{{ $link['url'] }}"
                        target="_new"
                >{{ $link['text'] }}</a>
            @endforeach
        </div>
    @endif
</div>
