@use(App\Casts\IdCard)
@use(App\Classes\IdCard\IdCardElement)
<div
    x-data="
    {
        draggingType: null,
        draggingContent: '',
        draggingRow: 0,
        draggingCol: 0
    }"
>
    <div class="list-group list-group-horizontal show-as-action border-bottom mb-2 pb-2">
        @foreach(IdCard::$sizes as $size)
            <div
                class="list-group-item list-group d-flex justify-content-center align-items-center list-group-item-action @if($idCard->aspectRatio == $size) active @endif"
                wire:key="{{ $size }}"
                wire:click="updateAspectRatio({{ $size }})"
            >
                <div
                    class="border border-info-subtle rounded border-3 p-2 mb-2 d-flex justify-content-center align-items-center"
                    style="aspect-ratio: {{ $size }}; border-style: dashed !important;">
                    <span class="text-info fs-5">1:{{ $size }}</span>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row" style="">
        <div class="col-3 border-end h-100 overflow-auto" style="max-height: 500px;">
            @include('livewire.people.id-creator.id-elements')
        </div>
        <div class="col-lg-6 pt-4 d-flex justify-content-center align-items-center h-100 p-2 position-relative">
            @if(!$viewing)
            <div
                class="card position-relative"
                style="aspect-ratio: {{ $idCard->aspectRatio }};
                background-color: {{ $idCard->getBackgroundRbga() }};
                @if($idCard->backgroundImage)
                background-image: url('{{ $idCard->backgroundImage }}');
                background-clip: border-box;
                background-size: cover;
                background-repeat: no-repeat;
                background-position: center;
                @endif
                background-blend-mode: {{ $idCard->backgroundBlendMode }};
                padding: {{ $idCard->contentPadding }}px;
                width: {{ $idWidth }}px;
                max-width: {{ $idWidth }}px;
                "
            >
                <table class="w-100 h-100" style="table-layout: fixed;">
                    @for($row = 0; $row < $idCard->rows; $row++)
                        <tr
                            class="@if($row < ($idCard->rows - 1))border-bottom border-bottom-3 border-bottom-dashed border-dark-subtle @endif p-0 m-0"
                        >
                            @for($col = 0; $col < $idCard->columns; $col++)
                                @continue((is_numeric($idCard->getContent($row,$col)) && $idCard->getContent($row,$col) == IdCard::CONTENT_SPAN))
                                <td
                                    class="position-relative
                                    @if($row == $selectedRow && $col == $selectedCol)  rounded border border-dark bg-dark @endif
                                    @if(($col + (($idCard->getContent($row,$col) instanceof IdCardElement)? ($idCard->getContent($row,$col)->colSpan - 1) : 0)) < ($idCard->columns - 1))
                                    border-end border-end-3 border-end-dashed border-dark-subtle
                                    @endif
                                    element-drop-target"
                                    droppable="true"
                                    @if($row == $selectedRow && $col == $selectedCol)
                                        style="--bs-bg-opacity: .10;"
                                    @endif
                                    x-on:dragover.prevent="$event.target.classList.add('bg-secondary');$event.dataTransfer.effectAllowed = 'move';"
                                    x-on:dragleave="$event.target.classList.remove('bg-secondary')"
                                    x-on:drop.prevent="
                                        if(draggingType === 'element')
                                            $wire.addElement(draggingContent, {{ $row }}, {{ $col }});
                                        else
                                            $wire.moveElement(draggingRow, draggingCol, {{ $row }}, {{ $col }});
                                    "
                                    @if($idCard->getContent($row,$col)instanceof \App\Classes\IdCard\IdCardElement)
                                    colspan="{{ $idCard->getContent($row,$col)->colSpan }}"
                                    rowspan="{{ $idCard->getContent($row,$col)->rowSpan }}"
                                    @endif
                                    x-on:dragstart="
                                        draggingType = 'content';
                                        draggingRow = {{ $row }};
                                        draggingCol = {{ $col }};
                                    "
                                >
                                    @if($idCard->getContent($row,$col) instanceof IdCardElement)
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div
                                                class="flex-grow-1 show-as-action d-flex justify-content-start align-items-center"
                                                @if($row == $selectedRow && $col == $selectedCol)
                                                    style="--bs-bg-opacity: .10;"
                                                wire:click="selectElement(-1, -1)"
                                                @else
                                                    wire:click="selectElement({{ $row }}, {{ $col }})"
                                                @endif
                                            >
                                                {!! $idCard->getContent($row,$col)->renderDummy() !!}
                                            </div>
                                        </div>
                                        @if($row == $selectedRow && $col == $selectedCol)
                                            <div
                                                class="show-as-grab position-absolute top-0 start-0 text-info"
                                                x-on:mousedown="$event.target.closest('.element-drop-target').setAttribute('draggable', 'true')"
                                                x-on:mouseup="$event.target.closest('.element-drop-target').setAttribute('draggable', 'false')"
                                            >
                                                <span class="fa-stack fa-2x" style="font-size: 0.7em !important;">
                                                    <i class="fa-solid fa-circle fa-stack-2x"></i>
                                                    <i class="fa-solid fa-arrows-up-down-left-right fa-stack-1x fa-inverse"></i>
                                                </span>
                                            </div>

                                            <div
                                                class="position-absolute top-0 start-50 translate-middle-x"
                                            >
                                                @if($row > 0)
                                                    <i
                                                        class="fa-solid fa-circle-chevron-up show-as-action"
                                                        wire:click="increaseRowSpanUp({{ $row }}, {{ $col }})"
                                                    ></i>
                                                @endif
                                                @if($idCard->getContent($row,$col)->rowSpan > 1)
                                                    <i
                                                        class="fa-solid fa-circle-chevron-down show-as-action"
                                                        wire:click="decreaseRowSpanDown({{ $row }}, {{ $col }})"
                                                    ></i>
                                                @endif
                                            </div>

                                            <div
                                                class="show-as-action position-absolute top-0 end-0 text-danger"
                                                wire:click="removeElement({{ $row }}, {{ $col }})"
                                            >
                                                <span class="fa-stack fa-2x" style="font-size: 0.7em !important;">
                                                    <i class="fa-solid fa-circle fa-stack-2x"></i>
                                                    <i class="fa-solid fa-times fa-stack-1x fa-inverse"></i>
                                                </span>
                                            </div>

                                            <div class="position-absolute top-50 start-0 translate-middle-y">
                                                @if($col > 0)
                                                    <i
                                                        class="fa-solid fa-circle-chevron-left show-as-action"
                                                        wire:click="increaseColSpanLeft({{ $row }}, {{ $col }})"
                                                    ></i>
                                                    <br />
                                                @endif
                                                @if($idCard->getContent($row,$col)->colSpan > 1)
                                                    <i
                                                        class="fa-solid fa-circle-chevron-right show-as-action"
                                                        wire:click="decreaseColSpanRight({{ $row }}, {{ $col }})"
                                                    ></i>
                                                @endif
                                            </div>

                                            <div class="position-absolute top-50 end-0 translate-middle-y">
                                                @if($col < ($idCard->columns - 1))
                                                    <i
                                                        class="fa-solid fa-circle-chevron-right show-as-action"
                                                        wire:click="increaseColSpan({{ $row }}, {{ $col }})"
                                                    ></i>
                                                    <br/>
                                                @endif
                                                @if($idCard->getContent($row,$col)->colSpan > 1)
                                                    <i
                                                        class="fa-solid fa-circle-chevron-left show-as-action"
                                                        wire:click="decreaseColSpan({{ $row }}, {{ $col }})"
                                                    ></i>
                                                @endif
                                            </div>

                                            <div class="position-absolute bottom-0 start-50 translate-middle-x">
                                                @if($row < ($idCard->rows - 1))
                                                    <i
                                                        class="fa-solid fa-circle-chevron-down show-as-action"
                                                        wire:click="increaseRowSpan({{ $row }}, {{ $col }})"
                                                    ></i>
                                                @endif
                                                @if($idCard->getContent($row,$col)->rowSpan > 1)
                                                    <i
                                                        class="fa-solid fa-chevron-up show-as-action"
                                                        wire:click="decreaseRowSpan({{ $row }}, {{ $col }})"
                                                    ></i>
                                                @endif
                                            </div>
                                        @endif
                                    @else
                                        <div class="w-100 h-100">&nbsp;</div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endfor
                </table>
                <button
                    class="position-absolute top-0 start-100 translate-middle btn btn-success btn-sm"
                    wire:click="$set('viewing', true)"
                >
                    <i class="fa-solid fa-eye"></i>
                </button>
                <button
                    class="position-absolute top-0 start-0 translate-middle btn btn-danger btn-sm"
                    wire:click="clear()"
                >
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
            @else
                <div class="z-3 bg-white border border-dark-subtle rounded p-2">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <livewire:people.person-selector />
                        <div class="btn-group mx-5">
                            @foreach(config('lms.id_sizes') as $size => $px)
                                <input
                                    type="radio"
                                    class="btn-check"
                                    name="idSize"
                                    id="{{$size}}"
                                    autocomplete="off"
                                    wire:model.live="viewingSize"
                                    value="{{ $size }}"
                                    wire:click="$set('viewingSize', '{{ $size }}')"
                                />
                                <label class="btn btn-outline-primary" for="{{$size}}">{{$size}}</label>
                            @endforeach
                        </div>
                        <button
                            class="btn btn-success btn-sm"
                            wire:click="$set('viewing', false)"
                        >
                            <i class="fa-solid fa-eye-slash"></i>
                        </button>
                    </div>
                    <x-people.id-viewer :idCard="$idCard" :person="$viewingPerson" :size="$viewingSize" />
                </div>
            @endif
        </div>
        <div class="col-3 border-start h-100 overflow-auto" style="max-height: 500px;">
            @include('livewire.people.id-creator.id-properties')
        </div>
    </div>
    <div class="row mt-3">
        <button type="button" class="btn @if($isSaved) btn-success @else btn-warning @endif col mx-2" wire:click="save()">
            @if($isSaved)
                {{ __('people.id.uptodate') }}
            @else
                {{ __('people.id.outdated') }}
            @endif
        </button>
        @if(!$isSaved)
            <button type="button" class="btn btn-secondary col mx-2" wire:click="revert()">
                {{ __('people.id.revert') }}
            </button>
        @endif
    </div>
</div>
@script
<script>
    $wire.on('person-selected', (event) =>
    {
        $wire.viewPerson(event.person.school_id);
    });

    $wire.on('person-cleared', (event) =>
    {
        $wire.clearViewing();
    });
</script>
@endscript
