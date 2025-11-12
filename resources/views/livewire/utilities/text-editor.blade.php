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
            toolbar_mode: 'wrap',
            toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link loadlmsimg | print preview media fullscreen | ' +
            'forecolor backcolor emoticons | help | inserttoken',
            relative_urls: false,
            remove_script_host: false,
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
                editor.ui.registry.addButton('loadlmsimg',
                {
                    icon: 'image',
                    tooltip: 'Insert image',
                    onAction: () =>
                    {
                        $wire.dispatch('document-storage-browser.open-browser',
                        {
                            config:
                            {
                                multiple: false,
                                mimeTypes: {{ \Illuminate\Support\Js::from(\App\Models\Utilities\MimeType::imageMimeTypes()) }},
                                allowUpload: true,
                                canSelectFolders: false,
                                cb_instance: '{!! $id !!}'
                            }
                        });
                    }
                });

                editor.addCommand('selectLmsImage', (file) =>
                {
                    let html = '<img src=\'' + file.url + '\' alt=\'' + file.name + '\' />';
                    editor.insertContent(html);
                    editor.setDirty(true);
                    editor.setProgressState(false);
                });

                @if($availableTokens)
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
                                        text: '{!! str_replace("'", "\\'", $tokenName) !!}',
                                        onAction: () => editor.insertContent('{!! $token !!}')
                                    },

                                    @endforeach
                                ];
                            callback(items);
                        }
                    });
                @endif
            },
            init_instance_callback: function (editor)
            {
                let MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;

                let observer = new MutationObserver(function (mutations, instance)
                {
                    let addedImages = [];
                    $.each(mutations, function (index, mutationRecord)
                    {
                        for (let i = 0; i < mutationRecord.addedNodes.length; i++)
                        {
                            let currentNode = mutationRecord.addedNodes[i];
                            if (currentNode.nodeName === 'IMG' && currentNode.className !== 'mce-clonedresizable')
                            {
                                if (addedImages.indexOf(currentNode.src) >= 0) continue;
                                addedImages.push(currentNode.getAttribute('src'));
                                continue;
                            }
                            let imgs = $(currentNode).find('img');
                            for (let j = 0; j < imgs.length; j++)
                            {
                                if (addedImages.indexOf(imgs[j].src) >= 0) continue;
                                addedImages.push(imgs[j].getAttribute('src'));
                            }
                        }
                    });

                    let removedImages = [];
                    $.each(mutations, function (index, mutationRecord)
                    {
                        for (let i = 0; i < mutationRecord.removedNodes.length; i++)
                        {
                            let currentNode = mutationRecord.removedNodes[i];
                            if (currentNode.nodeName === 'IMG' && currentNode.className !== 'mce-clonedresizable')
                            {
                                if (removedImages.indexOf(currentNode.src) >= 0) continue;
                                removedImages.push(currentNode.getAttribute('src'));
                                continue;
                            }
                            let imgs = $(currentNode).find('img');
                            for (let j = 0; j < imgs.length; j++)
                            {
                                if (removedImages.indexOf(imgs[j].src) >= 0) continue;
                                removedImages.push(imgs[j].getAttribute('src'));
                            }
                        }
                    });

                    if (removedImages.length > 1)
                        $wire.removeImages(removedImages);
                    else if (removedImages.length === 1)
                        $wire.removeImage(removedImages[0]);
                });

                observer.observe(editor.getBody(),
                {
                    childList: true,
                    subtree: true
                });
            },
            images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) =>
            {
                $wire.upload('uploadedFile', blobInfo.blob(),
                    async (uploadedFilename) => resolve(await $wire.uploadFile()),
                    () => reject('Upload failed'),
                    (event) => {},
                    () => reject('Upload failed'));
            }),
            images_reuse_filename: true,
            @verbatim
            noneditable_regexp: /\{\{\s*\$[a-zA-Z][a-zA-Z0-9_]*\s*\}\}|\{!!\s*\$[a-zA-Z][a-zA-Z0-9_]*\s*!!\}/gu
            @endverbatim
        })
    "
>
    <textarea id="{!! $id !!}" name="{!! $name !!}" x-bind:value="value"></textarea>
</div>
@script
<script>
    window.addEventListener('document-storage-browser.files-selected',
        (event) => (event.cb_instance === '{!! $id !!}') ? tinymce.get('{!! $id !!}').setProgressState(true) : null);
    $wire.on('text-editor.insert-image', (event) => tinymce.get('{!! $id !!}').execCommand('selectLmsImage', event.work_file));

</script>
@endscript
