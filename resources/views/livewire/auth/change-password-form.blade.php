<form wire:submit="resetPassword">
    <div class="row mb-3">
        <div class="col-md-8 align-content-center">
            @if($authFirst)
                <div class="mb-3">
                    <livewire:auth.password-field
                            wire:model="currentPassword"
                            :prepend-text="__('settings.auth.password.current')"
                            :required="true"
                            :validate="false"
                    />
                </div>
            @endif
            <div class="mb-3">
                <livewire:auth.password-field
                        wire:model.live="newPassword"
                        :prepend-text="__('settings.auth.password.new')"
                        :required="false"
                        :show-generate-password="true"
                />
            </div>
            <livewire:auth.password-field
                    wire:model.live="confirmPassword"
                    :prepend-text="__('settings.auth.password.confirm')"
                    required="true"
            />
        </div>
        <div class="col-md-4 align-content-center">
            <div class="p-2 m-0 alert @if(!$newPassword) alert-info @elseif(
                strlen($newPassword) >= $authSettings->min_password_length &&
                preg_match("/[A-Z]/", $newPassword) &&
                preg_match("/[0-9]/", $newPassword) &&
                preg_match("/[~!#$%\^&*()\-_.,<>?\/\\{}\[\]|:;]/", $newPassword) &&
                $newPassword == $confirmPassword) alert-success @else alert-danger @endif">
                <h6 class="alert-heading border-bottom border-info">{{ __('settings.auth.password.requirements') }}</h6>
                <ul class="list-group list-group-flush small">
                    <li class="ps-1 list-group-item @if(!$newPassword) list-group-item-info @elseif(strlen($newPassword) >= $authSettings->min_password_length) list-group-item-success @else list-group-item-danger @endif">
                        {{ __('settings.auth.password.requirements.min', ['length' => $authSettings->min_password_length]) }}
                    </li>
                    @if($authSettings->upper)
                        <li class="ps-1 list-group-item @if(!$newPassword) list-group-item-info @elseif(preg_match("/[A-Z]/", $newPassword)) list-group-item-success @else list-group-item-danger @endif">
                            {{ __('settings.auth.password.requirements.upper') }}
                        </li>
                    @endif
                    @if($authSettings->numbers)
                        <li class="ps-1 list-group-item @if(!$newPassword) list-group-item-info @elseif(preg_match("/[0-9]/", $newPassword)) list-group-item-success @else list-group-item-danger @endif">
                            {{ __('settings.auth.password.requirements.numbers') }}
                        </li>
                    @endif
                    @if($authSettings->symbols)
                        <li class="ps-1 list-group-item @if(!$newPassword) list-group-item-info @elseif(preg_match("/[~!#$%\^&*()\-_.,<>?\/\\{}\[\]|:;]/", $newPassword)) list-group-item-success @else list-group-item-danger @endif">
                            {{ __('settings.auth.password.requirements.symbols') }}
                        </li>
                    @endif
                    <li class="ps-1 list-group-item @if(!$newPassword) list-group-item-info @elseif($newPassword == $confirmPassword) list-group-item-success @else list-group-item-danger @endif">
                        {{ __('settings.auth.password.requirements.confirm') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @error('newPassword')
    <div class="row justify-content-center">
        <div class="col-8">
            <div class="alert alert-danger">
                {{ $errors->first('newPassword') }}
            </div>
        </div>
    </div>
    @enderror
    @error('currentPassword')
    <div class="row justify-content-center">
        <div class="col-8">
            <div class="alert alert-danger">
                {{ $errors->first('currentPassword') }}
            </div>
        </div>
    </div>
    @enderror
    @if($passwordChangedSuccessfully)
    <div class="row justify-content-center">
        <div class="col-8">
            <div class="alert alert-success">
                {{ __('settings.auth.password.change.success') }}
            </div>
        </div>
    </div>
    @endif
    <div class="row justify-content-center mt-4">
        <button type="submit" class="btn btn-primary col-6">{{ __('settings.auth.password.change') }}</button>
    </div>
</form>

