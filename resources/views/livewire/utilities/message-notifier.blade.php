<a class="nav-link p-0" href="{{ $url }}" role="button"
   x-on:message-notifier-new-message-alert="$el.classList.add('glowing'); setTimeout(() => $el.classList.remove('glowing'), 5000)"
>
    @if($notifications->count() == 0)
        <i
            class="fa-solid fa-comment fs-3 text-white"
        ></i>
    @else
        <span class="fa-layers fa-fw fs-3" id="new-message-alert">
            <i class="fa-solid fa-comment text-success-emphasis"></i>
            <span class="fa-layers-text text-white" data-fa-transform="shrink-8">{{ $notifications->count() }}</span>
        </span>
    @endif
</a>
