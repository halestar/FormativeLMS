<form action="{{ route('language.switch') }}" method="POST" class="inline-block"
      x-data="{ lang: '{{ app()->getLocale() }}'}" x-ref="langForm">
    @csrf
    <input type="hidden" name="lang" onchange="alert('here')" :value="lang"/>
    <li class="nav-item dropdown ms-3 " id="lang-menu">
        <a id="lang-selector" class="nav-link dropdown-toggle p-0" href="#" role="button" data-bs-toggle="dropdown"
           aria-expanded="false">
            <img src="/images/flags/{{ app()->getLocale() }}.svg" alt="{{ app()->getLocale() }}">
        </a>

        <ul class="dropdown-menu" aria-labelledby="lang-selector" id="lang-dropdown-container"
            style="min-width: fit-content">
            @foreach(config('app.available_locales') as $locale => $language)
                <li class="dropdown-item" @click="lang='{{ $locale }}'; $nextTick(() => { $refs.langForm.submit() });">
                    <img src="/images/flags/{{ $locale }}.svg" alt="{{ $language }}"></li>
            @endforeach
        </ul>
    </li>
</form>