<?php

namespace kabar\Widget\Widget\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Color picker widget field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */
class ColorPicker extends AbstractField
{

    const IN_FOOTER = true;

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
     * @param string    $default
     */
    public function __construct($id, $label, $default)
    {
        $this->id             = $id;
        $this->label          = $label;
        $this->default        = $default;

        add_action('customize_controls_enqueue_scripts', array($this, 'addScripts'));
    }

    /**
     * Adds color picker scripts
     */
    public function addScripts()
    {
        /**
         * @link http://www.dematte.at/tinyColorPicker/
         * @link https://github.com/PitPik/tinyColorPicker
         */
        wp_enqueue_script(
            'jquery-color-picker',
            $this->getAssetsUri() . 'js/vendor/jqColorPicker.min.js',
            array(),
            ServiceLocator::VERSION,
            self::IN_FOOTER
        );
        wp_enqueue_script(
            'widget-color-picker',
            $this->getAssetsUri() . 'js/ColorPicker.js',
            array('jquery-color-picker'),
            ServiceLocator::VERSION,
            self::IN_FOOTER
        );
    }

    /**
     * Returns widget field value
     * @param  array $args     Widget arguments.
     * @param  array $instance Saved values from database.
     * @return string
     */
    public function get($args, $instance)
    {
        return isset($instance[$this->id]) ? strtoupper($instance[$this->id]) : strtoupper($this->default);
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
        $template->value = empty($instance[$this->id]) ? $this->default : $instance[$this->id];
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
        $newInstance[$this->id] = (!empty($newInstance[$this->id])) ? strip_tags($newInstance[$this->id]) : '';

        return $newInstance;
    }
}
