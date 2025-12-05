<div
        class="{!! $classes !!}"
        style="{!! $style !!}"
        wire:ignore
        x-data="{ value: $wire.entangle('content') }"
        x-init="
        tinymce.init(
        {
            selector: '#{{ $id }}',
            license_key: 'gpl',
            promotion: false,
            width: '{{ $width }}',
            height: '{{ $height }}',
            plugins: [],
            toolbar_mode: 'scrolling',
            toolbar: 'inserttoken',
            relative_urls: false,
            remove_script_host: false,
            menubar: false,
            valid_elements: '',
            statusbar: false,
            setup: (editor) =>
            {
                editor.on('blur', (event) => value = editor.getContent());
                editor.on('init', (event) =>
                {
                    if(value != null)
                        editor.setContent(value)
                });
                $watch('value', (newValue) =>
                {
                    if (newValue !== editor.getContent())
                    {
                        editor.resetContent(newValue || '');
                        editor.selection.select(editor.getBody(), true);
                        editor.selection.collapse(false);
                    }
                });

                let toggleState = false;
                editor.ui.registry.addMenuButton('inserttoken',
                {
                    icon: 'addtag',
                    fetch: (callback) =>
                    {
                        const items =
                            [


                                @foreach($availableTokens as $token => $tokenName)


                                {
                                    type: 'menuitem',
                                    text: `{!! $tokenName !!}`,
                                    onAction: () => editor.insertContent('{!! $token !!}')
                                },

                                @endforeach


                            ];
                        callback(items);
                    }
                });
            },
            @verbatim
            noneditable_regexp: /\{\{\s*\$[a-zA-Z][a-zA-Z0-9_]*\s*\}\}|\{!!\s*\$[a-zA-Z][a-zA-Z0-9_]*\s*!!\}/gu
            @endverbatim
        })
    "
>
    <textarea id="{!! $id !!}" name="{!! $name !!}" x-bind:value="value"></textarea>
</div>