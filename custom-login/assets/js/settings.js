jQuery(document).ready(function($) {
    $('#col-color').wpColorPicker({
        defaultColor: '#fff'
    });

    wp.codeEditor.initialize($('#css_code'), cm_setting);
    


    var background_uploader;

    $('#background-selector').click(function() {
        if (background_uploader !== undefined) {
            background_uploader.open();
            return;
        }
        background_uploader = wp.media({
            title: 'Choose Background',
            button: {
                text: 'همینو انتخاب کن'
            },
            library: {
                type: 'image'
            }
        });

        background_uploader.on('select', function() {
            let selected = background_uploader.state().get('selection');
            $('#background').val(selected.first().toJSON().url);
            $('#background-preview').attr('src',selected.first().toJSON().url);
        });

        background_uploader.open();
    });
});