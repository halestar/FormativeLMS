<div class="w-100">
    @include('passkeys::components.partials.authenticateScript')

    <form id="passkey-login-form" method="POST" action="{{ route('passkeys.login') }}">
        @csrf
    </form>

    @if($message = session()->get('authenticatePasskey::message'))
        <div class="alert alert-danger shadow-sm mb-3" role="alert">
            {{ $message }}
        </div>
    @endif

    <div class="d-grid" onclick="authenticateWithPasskey()">
        @if ($slot->isEmpty())
            <button type="button" class="btn btn-outline-primary btn-lg w-100 text-start rounded-4 px-4 py-3 shadow-sm">
                <span class="d-flex align-items-center justify-content-between gap-3">
                    <span class="d-flex align-items-center gap-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 2.75rem; height: 2.75rem;">
                            <i class="fa-solid fa-key"></i>
                        </span>
                        <span>
                            <span class="d-block fw-semibold text-dark">{{ __('passkeys::passkeys.authenticate_using_passkey') }}</span>
                            <span class="d-block small text-body-secondary">{{ __('Login') }}</span>
                        </span>
                    </span>
                    <i class="fa-solid fa-chevron-right small text-body-secondary"></i>
                </span>
            </button>
        @else
            {{ $slot }}
        @endif
    </div>
</div>
