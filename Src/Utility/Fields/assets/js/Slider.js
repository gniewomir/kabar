(function($){
    $(function() {
        "use_strict";

        var $sliders = $('.kabar-Utility-Fields-Slider');

        $sliders.each(function(index) {
            var id             = $($sliders[index]).find('input').attr('id'),
                dataObjectName = id.replace(/-/g,'_'),
                data           = window[dataObjectName];

            $('.'+data.id).slider({
                min:   parseFloat(data.min),
                max:   parseFloat(data.max),
                step:  parseFloat(data.step),
                value: parseFloat(data.val),
                slide: function( event, ui ) {
                    $('#'+data.id).val(ui.value);
                    $('#'+data.id).trigger('change');
                },
            });

            $('#'+data.id).on(
                'keyup',
                function(event) {
                    $('.'+data.id).slider('value', $(this).val());
                }
            );

        });
    });
})(jQuery);
