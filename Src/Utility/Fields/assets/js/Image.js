(function($){
    $(function() {
        "use_strict";
        var buttonClass            = '.kabar-Utility-Fields-Image-button';
            backup_send_attachment = wp.media.editor.send.attachment,
            custom                 = true;

        // bind media library
        $('body').on(
            'click',
            buttonClass,
            function(event) {
                var inputId = $(this).prev().attr('id');
                wp.media.editor.open(inputId);
                wp.media.editor.send.attachment = function(props, attachment){
                    if (custom) {
                        $('#'+inputId).val(attachment.url);
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
