<script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script>
    function imagesUploadHandler(blobInfo, success, failure)
    {
        let xhr = new XMLHttpRequest();
        xhr.open('POST', '{{ route('cms.posts.upload') }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', $('meta[name=csrf-token]').attr('content')); // manually set header

        xhr.onload = function() {
            if (xhr.status !== 200) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }

            let json = JSON.parse(xhr.responseText);

            if (!json || typeof json.location !== 'string') {
                failure('Invalid JSON: ' + xhr.responseText);
                return;
            }

            success(json.location);
        };

        let formData = new FormData();
        formData.append('file', blobInfo.blob(), blobInfo.filename());

        xhr.send(formData);
    }

    tinymce.init({
        selector: 'textarea#teditor', // Replace this CSS selector to match the placeholder element for TinyMCE
        plugins: 'code table lists link fullscreen autolink charmap codesample image',
        license_key: 'gpl',
        file_picker_types: 'image',
        images_upload_url: '{{ route('cms.posts.upload') }}',
        image_uploadtab: true,
        images_upload_handler: imagesUploadHandler,
        image_list: '{{ route('cms.posts.list') }}',
        line_height_formats: '0.25 0.5 0.75 1 1.2 1.4 1.6 2',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | link image | code | lineheight | fullscreen',
    });
</script>
