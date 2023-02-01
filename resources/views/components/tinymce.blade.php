@push('scripts')
<script
    src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.9.5/tinymce.min.js?apiKey=tqmwustj9sc7yxkfpt6yqxx3la7if7r4ptt1pusfs3tio0s5">
</script>

<script>
    tinymce.init({
    selector: "textarea",
    setup: function (editor) {
    editor.on("change", function () {
    editor.save();
    });
    },

    image_class_list: [{ title: "Responsive", value: "img-fluid" }],
    plugins:
    "charmap code lists image paste fullscreen imagetools preview table textcolor",
    toolbar: [
    "code preview image removeformat | bold italic underline | subscript superscript charmap | alignleft aligncenter alignright | bullist numlist  selectall fullscreen"
    ],
    images_upload_url: "{{ config('app.postAcceptor_url') }}",
    images_upload_base_path: "/",
    images_upload_credentials: true,
    relative_urls: false,
    remove_script_host: true,
    convert_urls: true,
    height: 400,
    paste_data_images: true,
    branding: false,
    contextmenu: "link image imagetools table spellchecker",
    quickbars_selection_toolbar: "bold italic | quicklink h1 h2 h3",
    toolbar_drawer: "floating",
    menubar: false,
    statusbar: false,
    forced_root_block: "",
    force_br_newlines: false,
    force_p_newlines: true,
    });
</script>
@endpush
