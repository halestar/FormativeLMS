<a
    class="dropdown-item notification"
    href="{{ $notification->data['url']?? '#' }}"
    style="border-color: {{ $notification->data['borderColor'] }};"
>
    <div
        class="notification-header d-flex justify-content-between align-items-center"
        style="background-color: {{ $notification->data['bgColor'] }}; color: {{ $notification->data['textColor'] }};"
    >
        <strong>{{ $notification->data['title'] }}</strong>
        @if($notification->data['icon'])
            <span class="notification-icon">
                {!! $notification->data['icon'] !!}
            </span>
        @endif
    </div>
    <div class="notification-body">
        {{ $notification->data['message'] }}
    </div>
</a>
