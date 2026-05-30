<div>
    @if($active)
        <ul class="list-group list-group-flush hoverable">
            <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
                <div class="d-flex justify-content-between align-items-center">
                    <label>{{ __('features.substitutes.campuses') }}</label>
                    <span>{{ $substitute->campuses->pluck('name')->implode(', ') }}</span>
                </div>
            </li>
            <li class="list-group-item list-group-flush border-bottom mb-2 pb-1">
                <div class="d-flex justify-content-between align-items-center">
                    <label>{{ __('features.substitutes.verify.contact.heading') }}</label>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                    <span class="small {{ $substitute->email_confirmed ? 'text-success' : 'text-danger' }}">
                        <i class="bi bi-envelope-fill"></i>
                        <span class="ms-1">{{ __('people.profile.fields.email') }}</span>
                    </span>
                        <span class="small {{ $substitute->sms_confirmed ? 'text-success' : 'text-danger' }}">
                        <i class="bi bi-chat-dots-fill"></i>
                        <span class="ms-1">{{ __('settings.communications.sms') }}</span>
                    </span>
                    </div>
                </div>
            </li>
        </ul>

    @else
        <div class="alert alert-warning">
            {{ __('features.substitutes.old.notice') }}
        </div>
    @endif
</div>