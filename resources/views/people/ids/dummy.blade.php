@use(App\Casts\IdCard)
@use(App\Classes\IdCard\IdCardElement)
<div
        class="card"
        @if($idCard)
            style="aspect-ratio: {{ $idCard->aspectRatio }} !important;
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
                width: 400px !important;
                max-width: 400px;
                "
        @endif
>
    @if($idCard)
        <table style="width: 100%; height: 100%;table-layout: fixed;">
            @for($row = 0; $row < $idCard->rows; $row++)
                <tr style="padding: 0;margin: 0;">
                    @for($col = 0; $col < $idCard->columns; $col++)
                        @continue((is_numeric($idCard->getContent($row,$col)) && $idCard->getContent($row,$col) == IdCard::CONTENT_SPAN))
                        <td
                                style="position: relative;"
                                @if($idCard->getContent($row,$col)instanceof \App\Classes\IdCard\IdCardElement)
                                    colspan="{{ $idCard->getContent($row,$col)->colSpan }}"
                                rowspan="{{ $idCard->getContent($row,$col)->rowSpan }}"
                                @endif
                        >
                            @if($idCard->getContent($row,$col) instanceof IdCardElement)
                                <div class="d-flex justify-content-center align-items-center">
                                    <div
                                            class="flex-grow-1 show-as-action d-flex justify-content-start align-items-center"
                                    >
                                        {!! $idCard->getContent($row,$col)->renderDummy() !!}
                                    </div>
                                </div>
                            @else
                                <div style="width: 100%; height: 100%;">&nbsp;</div>
                            @endif
                        </td>
                    @endfor
                </tr>
            @endfor
        </table>
    @endif
</div>
