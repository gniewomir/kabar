(function($){
    $(document).ready(function() {
        var $sections = jQuery('.section-position-top');
            $profile  = jQuery('#your-profile');
        $profile.prepend($sections);
    });
})(jQuery);
