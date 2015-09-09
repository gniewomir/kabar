<?php

namespace kabar\Widget\Widget\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Image select/upload widget field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */
class Image extends AbstractField
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
     * Adds media uploader scripts
     */
    public function addScripts()
    {
        wp_enqueue_media();
        wp_enqueue_script(
            'widget-image-script',
            $this->getAssetsUri().'js/Image.js',
            array('media-upload', 'thickbox'),
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
        return isset($instance[$this->id]) ? esc_url($instance[$this->id], array('http', 'https')) : $this->default;
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
        $template->value = isset($instance[$this->id]) ? $instance[$this->id] : $this->default;
        $template->label = $this->label;
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
        $newInstance[$this->id] = isset($newInstance[$this->id]) ? esc_url($newInstance[$this->id]) : $this->default;

        return $newInstance;
    }
}
