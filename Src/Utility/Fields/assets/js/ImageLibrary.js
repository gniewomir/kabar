(function($){
    $(function() {
        "use_strict";
        var buttonClass            = '.kabar-Utility-Fields-ImageLibrary-button',
            removeButtonClass      = '.kabar-Utility-Fields-ImageLibrary-button-remove',
            backup_send_attachment = wp.media.editor.send.attachment,
            custom                 = true;

        // bind remove
        $('body').on(
            'click',
            removeButtonClass,
            function(event) {
                var inputId = $(this).prev().prev().attr('id');
                $('#'+inputId).parent().find('.image-preview > img').attr('src', '');
                $('#'+inputId).val('');
            }
        );

        // bind media library
        $('body').on(
            'click',
            buttonClass,
            function(event) {
                var inputId = $(this).prev().attr('id');
                wp.media.editor.open(inputId);
                wp.media.editor.send.attachment = function(props, attachment){
                    if (custom) {
                        $('#'+inputId).val(attachment.id);
                        $('#'+inputId).parent().find('.image-preview > img').attr('src', attachment.url);
                        $('#'+inputId).trigger('change');
                    } else {
                        return backup_send_attachment.apply( inputId, [props, attachment] );
                    }
                };
                return false;
            }
        );
    });
})(jQuery);
