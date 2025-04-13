<a class="nav-link p-0" href="{{ $url }}" role="button">
    @if($notifications->count() == 0)
        <i class="fa-solid fa-comment fs-3 text-white"></i>
    @else
        <span class="fa-layers fa-fw fs-3 @if($shouldGlow) glowing @endif" id="new-message-alert">
            <i class="fa-solid fa-comment text-success-emphasis"></i>
            <span class="fa-layers-text text-white" data-fa-transform="shrink-8">{{ $notifications->count() }}</span>
        </span>
    @endif
</a>
@script
<script>

    $wire.on('new-message-alert', () => {
        setTimeout(function()
        {
            $('#new-message-alert').removeClass('glowing');
        }, 5000)
    });

</script>
@endscript
