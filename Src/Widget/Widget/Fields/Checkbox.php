<?php

namespace kabar\Widget\Widget\Fields;

/**
 * Checkbox widget field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */
class Checkbox extends AbstractField
{

    const ON  = 'on';
    const OFF = 'off';

    /**
     * Field id
     * @var string
     */
    protected $id;

    /**
     * Field label
     * @var string
     */
    protected $label;

    /**
     * Field default value
     * @var string
     */
    protected $default;

    /**
     * Setup text field object
     * @param string    $id
     * @param string    $label
     */
    public function __construct($id, $label)
    {
        $this->id             = $id;
        $this->label          = $label;
    }

    /**
     * Returns widget field value
     * @param  array $args     Widget arguments.
     * @param  array $instance Saved values from database.
     * @return boolean
     */
    public function get($args, $instance)
    {
        return isset($instance[$this->id]);
    }

    /**
     * Field rendering for back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return kabar\Utility\Template\Template
     */
    public function form($instance)
    {
        $template = parent::form($instance);
        $template->checked  = isset($instance[$this->id]) ? 'checked' : '';
        $template->value    = self::ON;
        $template->label    = $this->label;
        return $template;
    }

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
        return $newInstance;
    }
}
