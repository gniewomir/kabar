(function(){
    jQuery( document ).ready(function() {
        var $sliders = jQuery('.kabar-Utility-Fields-Slider');
        $sliders.each(function(index) {
            var id             = jQuery($sliders[index]).find('input').attr('id'),
                dataObjectName = id.replace(/-/g,'_'),
                data           = window[dataObjectName];

            jQuery('.'+data.id).slider({
                min:   parseFloat(data.min),
                max:   parseFloat(data.max),
                step:  parseFloat(data.step),
                value: parseFloat(data.val),
                slide: function( event, ui ) {
                    jQuery('#'+data.id).val(ui.value);
                    jQuery('#'+data.id).trigger('change');
                },
            });

            jQuery('#'+data.id).on(
                'keyup',
                function(event) {
                    jQuery('.'+data.id).slider('value', jQuery(this).val());
                }
            );

        });
    });
})();
