tinymce.PluginManager.add('loadlmsimg', function (editor, url)
{
    editor.ui.registry.addButton('loadlmsimg',
{
        icon: 'image',
        tooltip: 'Insert image',
        onAction: function ()
        {
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

    return
    {
        getMetadata: () => ({
            name: 'Adds an image button to open and get a file or files from the LMS',
            url: 'http://dev.kalinec.net'
        })
    }
});