<?php

namespace kabar\Widget\Widget\Fields;

/**
 * Header widget field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */
class Header extends AbstractField
{

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
     * @return string
     */
    public function get($args, $instance)
    {
        return '';
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
        $template        = parent::form($instance);
        $template->label = $this->label;
        return $template;
    }

    /**
     * Front-end display of widget field.
     *
     * @see WP_Widget::widget()
     *
     * @param  array $args     Widget arguments.
     * @param  array $instance Saved values from database.
     * @return string
     */
    public function widget($args, $instance)
    {
        echo $this->get($args, $instance);
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
