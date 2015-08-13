jQuery(document).ready( function(){
    function bindMediaLibrary(buttonClass) {
        "use_strict";
        var backup_send_attachment = wp.media.editor.send.attachment,
            custom                 = true;

        jQuery('body').on(
            'click',
            buttonClass,
            function(event) {
                var inputId = jQuery(this).prev().attr('id');
                wp.media.editor.open(inputId);
                wp.media.editor.send.attachment = function(props, attachment){
                    if (custom) {
                        jQuery('#'+inputId).val(attachment.url);
                        jQuery('#'+inputId).trigger('change');
                    } else {
                        return backup_send_attachment.apply( inputId, [props, attachment] );
                    }
                };
                return false;
            }
        );
    }
    bindMediaLibrary('.kabar-Utility-Fields-Image-button');
});

