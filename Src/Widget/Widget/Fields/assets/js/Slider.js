(function(){
        jQuery( document ).on('widget-added', function ( event, widget ) {

            var $sliders = jQuery(widget).find('.kabar-Widget-Widget-Fields-Slider');

            $sliders.each(function(index) {
                var slug           = jQuery($sliders[index]).find('input').attr('id'),
                    $this          = jQuery(this);

                var dataObjectName = slug.replace(/-/g,'_');

                if (!window[dataObjectName]) {
                    console.log('No data object. Slider script aborted;');
                    return;
                }

                var data           = window[dataObjectName];

                var $sliderEl = $this.find('.'+data.fieldId).first(),
                    $inputEl  = $this.find('#'+slug).first();

                $sliderEl.slider({
                    min:   parseFloat(data.min),
                    max:   parseFloat(data.max),
                    step:  parseFloat(data.step),
                    value: parseFloat(data.val),
                    slide: function( event, ui ) {
                        $inputEl.val(ui.value);
                        $inputEl.trigger('change');
                    },
                });

                $inputEl.on(
                    'keyup',
                    function(event) {
                        $sliderEl.slider('value', jQuery(this).val());
                    }
                );

            });
        });
})();
