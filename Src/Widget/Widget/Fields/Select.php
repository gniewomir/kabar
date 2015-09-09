<?php

namespace kabar\Widget\Widget\Fields;

/**
 * Select widget field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */
class Select extends AbstractField
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
     * Options array coinsiting pairs label => value
     * @var array
     */
    protected $options;

    /**
     * Field help
     * @var string
     */
    protected $help;

    /**
     * Setup text field object
     * @param string    $id
     * @param string    $label
     * @param array     $options Options array coinsiting pairs label => value
     * @param string    $default
     */
    public function __construct($id, $label, $options, $default = null, $help = '')
    {
        $this->id      = $id;
        $this->label   = $label;
        $this->options = $options;
        $this->default = $default;
        $this->help    = $help;
    }

    /**
     * Returns widget field value
     * @param  array $args     Widget arguments.
     * @param  array $instance Saved values from database.
     * @return string
     */
    public function get($args, $instance)
    {
        return isset($instance[$this->id]) ? $instance[$this->id] : $this->default;
    }

    /**
     * Field rendering for back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return kabar\Component\Template\Template
     */
    public function form($instance)
    {
        $template          = parent::form($instance);
        $template->value   = empty($instance[$this->id]) ? $this->default : $instance[$this->id];
        $template->label   = $this->label;
        $template->options = $this->options;
        $template->default = $this->default;
        $template->help    = $this->help;
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
        $newInstance[$this->id] = isset($newInstance[$this->id]) ? esc_attr($newInstance[$this->id]) : $this->default;

        return $newInstance;
    }
}
