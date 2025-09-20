<div class="input-group" x-data="{ showPassword: {{ $clearPassword? 'true':'false' }} }">
    @if($prependText)
        <span class="input-group-text">{!! $prependText !!}</span>
    @endif
    <input
            id="{{ $id }}"
            wire:model.blur="password"
            @if($showClearPassword)
                :type="showPassword? 'text': 'password'"
            @endif
            class="form-control @if($validate) @error('password') is-invalid @elseif($password) is-valid @enderror @endif"
            name="{{ $name }}"
    />
    @if($showClearPassword)
        <button
                type="button"
                class="btn btn-light"
                x-on:click="showPassword = !showPassword"
        >
            <i x-show="showPassword" class="fa-solid fa-eye"></i>
            <i x-show="!showPassword" class="fa-solid fa-eye-slash"></i>
        </button>
    @endif
    @if($showGeneratePassword)
        <button
                type="button"
                class="btn btn-success"
                wire:click="generatePassword()"
        ><i class="fa-solid fa-dice"></i></button>
    @endif
</div>
