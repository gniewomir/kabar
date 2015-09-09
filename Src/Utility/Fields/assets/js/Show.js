(function($){
    $(function() {
        "use_strict";
        var $showFields = $('.kabar-Utility-Fields-Show'),
            $show       = $showFields.find('input');

        function hideOther($field) {
            $field.parent().find('.kabar-Utility-Fields').not('.kabar-Utility-Fields-Show').hide();
        }

        function showOther($field) {
            $field.parent().find('.kabar-Utility-Fields').not('.kabar-Utility-Fields-Show').show();
        }

        $showFields.each(
            function(index) {
                var $this    = $(this),
                    checked = $this.find('input').is(':checked');
                if (!checked) {
                    hideOther($this);
                }
            }
        );

        $show.on(
            'click.kabar-show-field',
            function(event) {
                var $this = $(this).closest('.kabar-Utility-Fields-Show'),
                    checked = $this.find('input').is(':checked');
                if (!checked) {
                    hideOther($this);
                } else {
                    showOther($this);
                }
            }
        );
    });
})(jQuery);
