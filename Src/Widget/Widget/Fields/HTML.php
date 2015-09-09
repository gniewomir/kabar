<?php

namespace kabar\Widget\Widget\Fields;

/**
 * Text area with HTML allowed widget field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */
class HTML extends AbstractField
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
     * Number of rows in textarea
     * @var int
     */
    protected $rows;

    /**
     * Field help
     * @var string
     */
    protected $help;

    /**
     * Setup text field object
     * @param string    $id
     * @param string    $label
     * @param string    $default
     * @param string    $help
     * @param int       $rows    Number of rows in textarea
     */
    public function __construct($id, $label, $default = '', $help = '', $rows = 8)
    {
        $this->id      = $id;
        $this->label   = $label;
        $this->default = $default;
        $this->help    = $help;
        $this->rows    = $rows;
    }

    /**
     * Returns widget field value
     * @param  array $args     Widget arguments.
     * @param  array $instance Saved values from database.
     * @return string
     */
    public function get($args, $instance)
    {
        return isset($instance[$this->id]) ? $this->doShortcodes($instance[$this->id]) : $this->doShortcodes($this->default);
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
        $template        = parent::form($instance);
        $template->value = empty($instance[$this->id]) ? $this->default : $instance[$this->id];
        $template->label = $this->label;
        $template->help  = $this->help;
        $template->rows  = $this->rows;
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
        $newInstance[$this->id] = (!empty($newInstance[$this->id])) ? wp_kses_post($newInstance[$this->id]) : '';
        return $newInstance;
    }
}
