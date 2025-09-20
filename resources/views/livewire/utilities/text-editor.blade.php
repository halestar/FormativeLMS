<div class="text-editor-container" wire:ignore>
    <textarea id="lms-text-editor" class="form-control">{!! $content !!}</textarea>
</div>
@script
<script>
    if (tinymce.activeEditor !== null)
        tinymce.EditorManager.execCommand('mceRemoveEditor', false, 'lms-text-editor');

    // prevent all the defaults first
    window.addEventListener("dragover", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);
    window.addEventListener("drop", function (e) {
        e = e || event;
        e.preventDefault();
    }, false);

    window.addEventListener('document-storage-browser.files-selected', (event) => (event.cb_instance === 'text-editor') ? tinymce.activeEditor.setProgressState(true) : null);
    $wire.on('text-editor.insert-image', (event) => tinymce.activeEditor.execCommand('selectLmsImage', event.work_file));

    tinymce.PluginManager.add('loadlmsimg', function (editor, url) {

        editor.ui.registry.addButton('loadlmsimg', {
            icon: 'image',
            tooltip: 'Insert image',
            onAction: function () {
                $wire.dispatch('document-storage-browser.open-browser',
                    {
                        config:
                            {
                                multiple: false,
                                mimeTypes: {{ \Illuminate\Support\Js::from(\App\Models\Utilities\MimeType::imageMimeTypes()) }},
                                allowUpload: true,
                                canSelectFolders: false,
                                cb_instance: 'text-editor'
                            }
                    });
            },
        });


        editor.addCommand('selectLmsImage', function (file) {
            let html = '<img src="' + file.url + '" alt="' + file.name + '" />';
            editor.insertContent(html);
            editor.setDirty(true);
            editor.setProgressState(false);
        });

        setInterval((editor) => {
            if (tinymce.activeEditor.isDirty()) {
                $wire.set('content', tinymce.activeEditor.getContent());
                tinymce.activeEditor.setDirty(false)
            }
        }, 500);

        @if($availableTokens)
        let toggleState = false;

        editor.ui.registry.addMenuButton('inserttoken',
            {
                icon: 'addtag',
                fetch: (callback) => {
                    const items =
                        [

                                @foreach($availableTokens as $token => $name)


                            {
                                type: 'menuitem',
                                text: "{!! $name !!}",
                                onAction: () => editor.insertContent("{!! $token !!}")
                            },



                            @endforeach

                        ];
                    callback(items);
                }
            });
        @endif

            return
        {
            getMetadata: () => ({
                name: 'Adds an image button to open and get a file or files from the LMS',
                url: 'http://dev.kalinec.net'
            })
        }
    });


    tinymce.init({
        selector: '#lms-text-editor',
        license_key: "gpl",
        promotion: false,
        plugins:
            [
                'advlist', 'autolink', 'link', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
                'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
                'media', 'table', 'emoticons', 'help', 'loadlmsimg'
            ],
        toolbar_mode: 'wrap',
        toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
            'bullist numlist outdent indent | link loadlmsimg | print preview media fullscreen | ' +
            'forecolor backcolor emoticons | help | inserttoken',
        relative_urls: false,
        remove_script_host: false,
        init_instance_callback: function (editor) {
            let MutationObserver = window.MutationObserver || window.WebKitMutationObserver || window.MozMutationObserver;

            let observer = new MutationObserver(function (mutations, instance) {
                let addedImages = [];
                $.each(mutations, function (index, mutationRecord) {
                    for (let i = 0; i < mutationRecord.addedNodes.length; i++) {
                        let currentNode = mutationRecord.addedNodes[i];
                        if (currentNode.nodeName === 'IMG' && currentNode.className !== "mce-clonedresizable") {
                            if (addedImages.indexOf(currentNode.src) >= 0) continue;

                            addedImages.push(currentNode.getAttribute("src"));
                            continue;
                        }
                        let imgs = $(currentNode).find('img');
                        for (let j = 0; j < imgs.length; j++) {
                            if (addedImages.indexOf(imgs[j].src) >= 0) continue;
                            addedImages.push(imgs[j].getAttribute("src"));
                        }
                    }
                });

                let removedImages = [];
                $.each(mutations, function (index, mutationRecord) {
                    for (let i = 0; i < mutationRecord.removedNodes.length; i++) {
                        let currentNode = mutationRecord.removedNodes[i];
                        if (currentNode.nodeName === 'IMG' && currentNode.className !== "mce-clonedresizable") {
                            if (removedImages.indexOf(currentNode.src) >= 0) continue;
                            removedImages.push(currentNode.getAttribute("src"));
                            continue;
                        }
                        let imgs = $(currentNode).find('img');
                        for (let j = 0; j < imgs.length; j++) {
                            if (removedImages.indexOf(imgs[j].src) >= 0) continue;
                            removedImages.push(imgs[j].getAttribute("src"));
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
        images_upload_handler: (blobInfo, progress) => new Promise((resolve, reject) => {
            $wire.upload('uploadedFile', blobInfo.blob(), async (uploadedFilename) => resolve(await $wire.uploadFile()),
                () => reject('Upload failed'),
                (event) => {
                },
                () => reject('Upload failed'));
        }),
        images_reuse_filename: true,
        @verbatim
        noneditable_regexp: /\{\{\s*\$[a-zA-Z][a-zA-Z0-9_]*\s*\}\}|\{!!\s*\$[a-zA-Z][a-zA-Z0-9_]*\s*!!\}/gu
        @endverbatim
    });


</script>
@endscript
