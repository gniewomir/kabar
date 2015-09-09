<?php
/**
 * Widget text field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */

namespace kabar\Widget\Widget\Fields;

/**
 * Text field class
 */
class Text extends AbstractField
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
     * @param string $id
     * @param string $label
     * @param string $default
     */
    public function __construct($id, $label, $default = '')
    {
        $this->id             = $id;
        $this->label          = $label;
        $this->default        = $default;
    }

    /**
     * Returns widget field value
     * @param  array $args     Widget arguments.
     * @param  array $instance Saved values from database.
     * @return string
     */
    public function get($args, $instance)
    {
        if ($this->id == 'title' && !empty($instance[$this->id])) {
            return $args['before_title'].apply_filters('widget_title', $instance[$this->id]).$args['after_title'];
        }

        return isset($instance[$this->id]) ? esc_html($instance[$this->id]) : $this->default;
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
        $template = parent::form($instance);
        $template->value   = empty($instance[$this->id]) ? $this->default : $instance[$this->id];
        $template->label   = $this->label;
        $template->default = $this->default;
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
        $newInstance[$this->id] = (!empty($newInstance[$this->id])) ? strip_tags($newInstance[$this->id]) : '';

        return $newInstance;
    }
}
