(function(){
    'use_strict';
    jQuery( document ).ready(function() {
        var $maps = jQuery('.kabar-Component-GoogleMap-GoogleMap .google-map');
        $maps.each(function(index) {
            var id             = jQuery($maps[index]).attr('id'),
                dataObjectName = id.replace(/-/g,'_');

            var data        = window[dataObjectName],
                latitude    = parseFloat(data.latitude),
                longitude   = parseFloat(data.longitude),
                zoom        = parseInt(data.zoom),
                scrollwheel = data.scrollwheel == 'true';

            if (!data.latitude || !data.longitude || !data.zoom) {
                console.log('No data for map "'+id+'"');
                return;
            }

            var mapCanvas = document.getElementById(id),
                mapOptions = {
                    center:      new google.maps.LatLng(latitude, longitude),
                    zoom:        zoom,
                    scrollwheel: scrollwheel,
                    mapTypeId:   google.maps.MapTypeId.ROADMAP
                },
                map = new google.maps.Map(mapCanvas, mapOptions);
        });
    });
})();
