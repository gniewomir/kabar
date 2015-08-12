jQuery(document).ready( function(){

    jQuery('.kabar-Utils-Fields-ColorPicker').each(function() {
        var id = '#'+jQuery(this).find('input').attr('id');
        jQuery(id).colorPicker({
            customBG: '#FFF',
            cssAddon: '.cp-color-picker { z-index: 999999; border-radius: 5px; background: #FFF; border: 1px solid #CCC;  }',
            colorMode: 'HEX',
            opacity: false,
            renderCallback: function($el, toggled) {
                var colors = this.color.colors,
                    rgb = colors.RND.rgb;

                jQuery($el).css({
                    backgroundColor: '#' + colors.HEX,
                    color: colors.RGBLuminance > 0.22 ? '#222' : '#ddd'
                }).val('#' + colors.HEX);

                if (toggled == false) {
                    jQuery($el).trigger('change');
                }

            }
        });

    });
});