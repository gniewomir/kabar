jQuery(document).ready( function(){
    var $ = jQuery;

    function incId($el) {
        var $idAttr    = $el.find('[id]'),
            $forAttr   = $el.find('[for]'),
            idArr      = $idAttr.attr('id').split('-'),
            number     = idArr[idArr.length-1],
            newNumber  = parseInt(number)+1;

        idArr[idArr.length-1] = newNumber;
        var id = idArr.join('-');
        $idAttr.attr('id',   id);
        $forAttr.attr('for', id);

        return newNumber;
    }

    function incName($el, newNumber) {

        var $nameAttr = $el.find('[name]');

        if ($nameAttr.length <= 0) {
            return;
        }

        var nameArr   = $nameAttr.attr('name').split('[');

        nameArr[nameArr.length-1] = newNumber + ']';
        var name = nameArr.join('[');
        $nameAttr.attr('name', name);
    }

    function bindRepeaters() {
        $('.kabar-Widget-Widget-Fields-Repeater .addLink').off('click');
        $('.kabar-Widget-Widget-Fields-Repeater .addLink').on(
            'click',
            function(event) {
                var $lastFieldset = $(this).closest('.kabar-Widget-Widget-Fields-Repeater').find('fieldset').last(),
                    $newFieldset  = $lastFieldset.clone();
                $lastFieldset.after($newFieldset);

                $newFieldset.find('.kabar-Widget-Widget-Fields').each(
                    function() {
                        var $this  = $(this),
                            newNumber = incId($this);

                        incName($this, newNumber);
                    }
                );
                event.preventDefault();
            }
        );
        $('.kabar-Widget-Widget-Fields-Repeater .rmLink').off('click');
        $('.kabar-Widget-Widget-Fields-Repeater .rmLink').on(
            'click',
            function(event) {
                var $lastFieldset = $(this).closest('.kabar-Widget-Widget-Fields-Repeater').find('fieldset').last();
                $lastFieldset.remove();
                event.preventDefault();
            }
        );
    }

    bindRepeaters();
    $(document).on(
        'widget-added',
        function ( event, widget ) {
            console.log('bind');
            bindRepeaters();
        }
    );
});