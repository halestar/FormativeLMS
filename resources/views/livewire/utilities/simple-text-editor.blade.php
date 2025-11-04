<div class="text-editor-container" wire:ignore>
    <textarea
        id="{{ $instance }}"
        name="{{ $name }}"
        class="{{ $classes }}"
        rows="{{ $rows }}"
        style="{{ $style }}"
    ></textarea>
</div>
@script
<script>
    if (tinymce.get('{{ $instance }}') != null)
        tinymce.EditorManager.execCommand('mceRemoveEditor', false, '{{ $instance }}');

    tinymce.init({
        selector: '#{{ $instance }}',
        license_key: "gpl",
        promotion: false,
        plugins:
            [
                'advlist', 'autolink', 'link', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
                'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
                'media', 'table', 'emoticons', 'help'
            ],
        toolbar_mode: 'wrap',
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link | print preview media fullscreen | ' +
            'forecolor backcolor emoticons | help',
        relative_urls: false,
        remove_script_host: false,
        setup: (editor) =>
        {
            editor.on('Change', () => {
                console.log('Content changed');
                $wire.set('content', editor.getContent());
            })
        }
    });


</script>
@endscript