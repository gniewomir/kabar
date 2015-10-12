<?php

namespace kabar\Widget\Widget\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Slider widget field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */
class Slider extends AbstractField
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
     * Slider start value
     * @var integer
     */
    protected $min;

    /**
     * Slider end value
     * @var integer
     */
    protected $max;

    /**
     * Slider step
     * @var integer
     */
    protected $step;

    /**
     * Setup text field object
     * @param string  $id
     * @param string  $label
     * @param string  $default
     * @param integer $min
     * @param integer $max
     * @param integer $step
     */
    public function __construct($id, $label, $default, $min, $max, $step)
    {
        $this->id             = $id;
        $this->label          = $label;
        $this->default        = $default;

        $this->min            = $min;
        $this->max            = $max;
        $this->step           = $step;

        add_action('customize_controls_enqueue_scripts', array($this, 'addScripts'));
        add_action('customize_controls_enqueue_scripts', array($this, 'addStyles'));
    }

    /**
     * Adds field scripts
     */
    public function addScripts()
    {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-slider');

        wp_enqueue_script(
            $this->getLibrarySlug().'-widget-field-slider-script',
            $this->getAssetsUri().'js/Slider.js',
            array('jquery-ui-core', 'jquery-ui-slider'),
            $this->getLibraryVersion(),
            self::IN_FOOTER
        );
    }

    /**
     * Adds field styles
     */
    public function addStyles()
    {
        wp_enqueue_style(
            'kabar-field-slider-styles',
            $this->getAssetsUri().'css/Slider.css',
            array(),
            ServiceLocator::VERSION
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
        return isset($instance[$this->id]) ? $instance[$this->id] : $this->default;
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
        $template->label = $this->label;
        $template->min   = $this->min;
        $template->max   = $this->max;
        $template->step  = $this->step;
        $template->value = isset($instance[$this->id]) ? $instance[$this->id] : $this->default;
        $template->slug  = $this->id;
        $template->script = <<<EOT
<script>
    {$this->getJavaScriptDataObjectName()} = {
        fieldId: '{$this->id}',
        min:     {$template->min},
        max:     {$template->max},
        step:    {$template->step},
        val:     {$template->value},
    }
</script>
EOT;
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
        if (isset($newInstance[$this->id])) {
            if (!is_numeric($newInstance[$this->id])) {
                $newInstance[$this->id] = $this->default;
            }

            if ($newInstance[$this->id] > $this->max) {
                $newInstance[$this->id] = $this->default;
            }

            if ($newInstance[$this->id] < $this->min) {
                $newInstance[$this->id] = $this->default;
            }
        }

        return $newInstance;
    }
}
