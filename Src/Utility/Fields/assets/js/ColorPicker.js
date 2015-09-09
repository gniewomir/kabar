(function($){
    $(function() {
        "use_strict";
        $('.kabar-Utility-Fields-ColorPicker').each(function() {
            var id = '#'+$(this).find('input').attr('id');
            $(id).colorPicker({
                customBG: '#FFF',
                cssAddon: '.cp-color-picker { z-index: 999999; border-radius: 5px; background: #FFF; border: 1px solid #CCC;  }',
                colorMode: 'HEX',
                opacity: false,
                renderCallback: function($el, toggled) {
                    var colors = this.color.colors,
                        rgb = colors.RND.rgb;
                    $($el).css({
                        backgroundColor: '#' + colors.HEX,
                        color: colors.RGBLuminance > 0.22 ? '#222' : '#ddd'
                    }).val('#' + colors.HEX);
                    if (toggled == false) {
                        $($el).trigger('change');
                    }
                }
            });
        });
    });
})(jQuery);