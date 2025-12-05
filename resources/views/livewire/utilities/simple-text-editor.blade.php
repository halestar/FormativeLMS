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
            plugins:
            [
                'advlist', 'autolink', 'link', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
                'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
                'media', 'table', 'emoticons', 'help'
            ],
            toolbar_mode: 'scrolling',
            toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link | ' +
            'forecolor backcolor',
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

            },
        })
    "
>
    <textarea id="{!! $id !!}" name="{!! $name !!}" x-bind:value="value"></textarea>
</div>