<li
    class="nav-item dropdown ms-3 my-auto @if($notifications->count() == 0) d-none @endif"
    x-data="{ unreadCount: $wire.entangle('unreadCount'), showing: $wire.entangle('showing')  }"
    @click.outside="showing = false"
>

    <a
        id="user-notifications"
        class="nav-link position-relative p-0 m-0"
        href="#"
        :class="showing && 'show'"
        @click.prevent="unreadCount = 0; showing = !showing"
        :class="(unreadCount > 0)? 'text-bright-alert': 'text-dark'"
    >
        <i class="fa-solid fa-bell fs-3"></i>
        <span
                class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle"
                id="notification-count"
                x-show="unreadCount > 0"
                x-text="unreadCount"
        ></span>
    </a>
    <div class="dropdown-menu dropdown-menu-end p-0 rounded" data-bs-theme="light" :class="showing && 'show'"
        style="width: 400px;" x-bind:data-bs-popper="showing && 'static'"
    >
        @foreach($notifications as $notification)
            <div
                class="dropdown-item notification show-as-action m-0 @if($loop->first) rounded-top @endif"
                wire:key="{{ $notification->id }}"
            >
                <div class="notification-header d-flex justify-content-between align-items-center text-bg-primary">
                    <strong>{!! $notification->data['short_subject'] !!}</strong>
                    <span
                        class="notification-icon"
                        x-data="{ hovering: false }"
                        @mouseenter="hovering = true"
                        @mouseleave="hovering = false"
                        wire:click="removeNotification('{{ $notification->id }}')"
                    >
                        <i x-show="hovering" class="text-danger fa-solid fa-circle-xmark"></i>
                        <i x-show="!hovering" class="fa-regular fa-message"></i>
                    </span>
                </div>
                <div
                    class="notification-body text-wrap bg-light w-100"
                    @if($notification->data['action_link'])
                        @click="window.location.href = '{{ $notification->data['action_link'] }}'"
                    @endif
                >
                    {!! $notification->data['short_body'] !!}
                </div>
            </div>
        @endforeach
        @if($notifications->count() > 0)
            <div
                    class="dropdown-item notification m-0 rounded-bottom"
            >
                <button type="button" class="btn btn-danger w-100 rounded-top-0" wire:click="removeAllNotifications">{{ __('common.clear') }}</button>
            </div>
        @endif
    </div>
</li>