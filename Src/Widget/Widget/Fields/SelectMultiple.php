<?php
/**
 * Multiple select widget field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */

namespace kabar\Widget\Widget\Fields;

/**
 * Multiple select class
 */
class SelectMultiple extends Select
{
    /**
     * Sanitize widget field values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $newInstance Values just sent to be saved.
     * @param array $oldInstance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($newInstance, $oldInstance)
    {
        $newInstance[$this->id] = isset($newInstance[$this->id]) ? $newInstance[$this->id] : array();

        return $newInstance;
    }
}
