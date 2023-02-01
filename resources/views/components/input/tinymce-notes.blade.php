

<div
x-data="{ value: @entangle($attributes->wire('model')) }"
x-init="
    tinymce.init({
        target: $refs.tinymce,
        setup: function(editor) {
            editor.on('blur', function(e) {
                value = editor.getContent()
            })
            editor.on('init', function (e) {
                editor.setContent(value)
            })
        },
        images_upload_url: '{{ route('upload-tinymce-notes-image') }}',
        images_upload_base_path: '/',
        plugins: [
            'charmap code lists image paste fullscreen imagetools preview table textcolor autoresize'
        ],
        toolbar: 'code preview image removeformat | bold italic underline | subscript superscript charmap | alignleft aligncenter alignright | bullist numlist  selectall fullscreen',
        images_upload_credentials: true,
        relative_urls: false,
        remove_script_host: true,
        convert_urls: true,
        paste_data_images: true,
        branding: false,
        contextmenu: 'link image imagetools table spellchecker',
        quickbars_selection_toolbar: 'bold italic | quicklink h1 h2 h3',
        toolbar_drawer: 'floating',
        menubar: false,
        statusbar: true,
        forced_root_block: '',
        force_br_newlines: false,
        force_p_newlines: true,
        autoresize_on_init: true
    })
"
wire:ignore
>
<div>
    <input x-ref="tinymce" type="textarea" {{ $attributes->whereDoesntStartWith('wire:model') }} >
</div>
