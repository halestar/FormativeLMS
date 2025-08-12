<div class="border rounded m-1 p-2 @if(!$person->auth_driver) text-bg-danger @elseif($person->auth_driver->isLocked()) text-bg-warning @else text-bg-secondary @endif">
    <h4 class="border-bottom">{{ __('settings.auth.user') }}</h4>
    @if($changingAuth)
        <div class="mb-3">
            <label for="change_auth" class="form-label">{{ __('settings.auth.change.to') }}</label>
            <select id="change_auth" class="form-select" wire:model="authDriver">
                @foreach(\App\Classes\Auth\Authenticator::all() as $driver => $driverClass)
                    <option value="{{ $driver }}" @selected($driver == $authDriver)>{{ ($driverClass)::driverPrettyName() }}</option>
                @endforeach
            </select>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-primary" wire:click="applyChangeAuth">{{ __('common.cancel') }}</button>
            <button type="button" class="btn btn-secondary" wire:click="cancelChangeAuth">{{ __('common.assign') }}</button>
        </div>
    @elseif($changingPasswd)
        <form wire:submit="resetPassword">
            <div class="mb-3">
                <label for="change_auth" class="form-label">{{ __('settings.auth.password.change') }}</label>
                <livewire:auth.password-field wire:model="newPassword" show-clear-password="true" show-generate-password="true" />
            </div>
            @if($person->auth_driver->canSetMustChangePassword())
                <div class="form-check mb-3">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        wire:model="mustChangePassword"
                        id="mustChangePassword"
                    />
                    <label for="mustChangePassword">{{ __('settings.auth.password.force') }}</label>
                </div>
            @endif
            <div class="d-flex justify-content-between align-items-center">
                <button type="submit" class="btn btn-primary">{{ __('common.change') }}</button>
                <button type="button" class="btn btn-secondary" wire:click="cancelChangePassword">{{ __('common.cancel') }}</button>
            </div>
        </form>
    @else
        @if(!$person->auth_driver)
            <h5>{{ __('settings.auth.reset.success') }}</h5>
        @else
            <h5 class="mb-3 text-info-emphasis fw-bold">{{ $person->auth_driver::driverPrettyName() }}</h5>
            <div class="alert alert-info">
                {{ $person->auth_driver::driverDescription() }}
            </div>
        @endif
        <div class="d-grid gap-2">
            @if($person->auth_driver != null)
                @if($person->auth_driver->isLocked())
                    <button class="btn btn-danger" type="button" wire:click="unlockUser"><b>{{ __('common.locked') }}</b>
                        {{ __('settings.auth.lock') }}</button>
                @else
                    <button class="btn btn-success" type="button" wire:click="lockUser"><b>{{ __('common.unlocked') }}</b>
                        {{ __('settings.auth.unlock') }}</button>
                    <button
                            class="btn btn-primary"
                            type="button"
                            wire:confirm="Are you sure you wish to reset this account? It will force the user to-relog."
                            wire:click="resetAuth()"
                    >{{ __('settings.auth.reset.confirm') }}</button>
                    @if($person->auth_driver->canChangePassword())
                        <button
                            class="btn @if($passwordWasChanged) btn-success @else btn-danger @endif"
                            id="change_passwd_btn"
                            type="button"
                            wire:click="changePassword"
                        >@if($passwordWasChanged) {{ __('settings.auth.password.changed') }} @else {{ __('settings.auth.password.change') }} @endif</button>
                    @endif
                @endif
            @endif
            <button class="btn btn-warning" type="button" wire:click="changeAuth">{{ __('settings.auth.change') }}</button>
        </div>
    @endif
</div>
@script
<script>
    $wire.on('user-auth-manager.password-changed', () => {
        setTimeout(function()
        {
            $('#change_passwd_btn').removeClass('btn-success').addClass('btn-danger').html('{{ __('settings.auth.password.change') }}');
        }, 5000)
    });
</script>
@endscript
