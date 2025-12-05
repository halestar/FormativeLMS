<div class="{{ $classes }}" style="{{ $style }}">
    <div class="d-flex justify-content-between align-items-center border-bottom">
        <h3>{{ __('subjects.school.widgets.class-links') }}</h3>
        @if($canManage)
            <div class="text-end">
                <button class="btn btn-primary btn-sm" wire:click="set('adding', true)"><i class="fa-solid fa-plus"></i></button>
            </div>
        @endif
    </div>
    @if($canManage && ($adding || $editing))
        <div class="border rounded bg-light p-3">
            <div class="mb-3">
                <label for="link_text" class="form-label">{{ __('subjects.school.widgets.class-links.text') }}</label>
                <input type="text" id="link_text" class="form-control @error('linkText') is-invalid @enderror"
                       wire:model="linkText"/>
                <x-utilities.error-display key="linkText">{{ $errors->first('linkText') }}</x-utilities.error-display>
            </div>
            <div class="mb-3">
                <label for="link_url" class="form-label">{{ __('subjects.school.widgets.class-links.link') }}</label>
                <input type="text" id="link_url" class="form-control @error('linkUrl') is-invalid @enderror"
                       wire:model="linkUrl"/>
                <x-utilities.error-display key="linkUrl">{{ $errors->first('linkUrl') }}</x-utilities.error-display>
            </div>
            <div class="row">
                @if($adding)
                    <button type="button" class="btn btn-primary col mx-2"
                            wire:click="addLink()">{{ __('common.add') }}</button>
                @else
                    <button type="button" class="btn btn-primary col mx-2" wire:click="updateLink()">
                        {{ __('common.update') }}</button>
                @endif
                <button type="button" class="btn btn-secondary col mx-2" wire:click="clearLinkForm()">
                    {{ __('common.cancel') }}</button>
            </div>
            @if($adding && $classSession->viewingAs(\App\Enums\ClassViewer::FACULTY))
                <div class="mt-3 pt-3 border-top">
                    <h4>{{ __('subjects.school.widgets.class-announcements.post.also') }}</h4>
                    <div class="row row-cols-3">
                        @foreach($self->currentClassSessions as $session)
                            <div class="form-check col">
                                <input class="form-check-input" type="checkbox" id="ow-{{ $session->id }}"
                                       value="{{ $session->id }}" wire:model="alsoPost"/>
                                <label class="form-check-label" for="ow-{{ $session->id }}">
                                    {{ $session->name_with_schedule }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
    @if($canManage)
        <ul class="list-group mt-3">
            @foreach($links as $link)
                <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                        wire:key="{{ $link->id }}"
                >
                    {{ $link->title }}
                    <div class="link-control">
                        <button
                                type="button"
                                class="btn btn-primary btn-sm"
                                wire:click="setEdit('{{ $link->id }}')"
                        ><i class="fa fa-edit"></i></button>
                        <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                wire:click="deleteLink('{{ $link->id }}')"
                                wire:confirm="{{ __('subjects.school.widgets.class-links.delete.prompt') }}"
                        ><i class="fa fa-times"></i></button>
                        <a
                                type="button"
                                class="btn btn-success btn-sm"
                                href="{{ $link->url }}"
                                target="_new"
                        ><i class="fa-solid fa-right-long"></i></a>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <div class="list-group mt-3">
            @foreach($links as $link)
                <a
                    class="list-group-item list-group-item-action"
                    wire:key="{{ $link->id }}"
                    href="{{ $link->url }}"
                    target="_new"
                >{{ $link->title }}</a>
            @endforeach
        </div>
    @endif
</div>
