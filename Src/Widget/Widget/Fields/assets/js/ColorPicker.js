jQuery(document).ready( function(){
    jQuery('.jqColorPicker').colorPicker({
        customBG: '#fff',
        cssAddon: '.cp-color-picker { z-index: 999999; border-radius: 5px; background: #FFF; border: 1px solid #CCC;  }',
        colorMode: 'HEX',
        opacity: false,
        renderCallback: function($elm, toggled) {
            var colors = this.color.colors,
                rgb = colors.RND.rgb;

            jQuery($elm).css({
                backgroundColor: '#' + colors.HEX,
                color: colors.RGBLuminance > 0.22 ? '#222' : '#ddd'
            }).val('#' + colors.HEX);

            if (toggled == false) {
                jQuery($elm).trigger('change');
            }

        }
    });
});