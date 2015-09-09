<?php

namespace kabar\Widget\Widget\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Repeater field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.9.42
 * @package    kabar
 * @subpackage WidgetFields
 */
class Repeater extends AbstractField
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
     * Default amount of fields in repeater
     * @var integer
     */
    protected $defaulCount;

    /**
     * Minimum number of repeats
     * @var int
     */
    protected $min;

    /**
     * Maximum number of repeats
     * @var int
     */
    protected $max;

    /**
     * Label for adding new fieldset
     * @var string
     */
    protected $addLabel;

    /**
     * Label for removing fieldset
     * @var string
     */
    protected $rmLabel;

    /**
     * Fields
     * @var array<\kabar\Widget\Widget\Fields\AbstractField>
     */
    protected $fields;

    /**
     * Setup repeater field
     * @param string  $id
     * @param string  $label
     * @param integer $defaultCount
     * @param integer $min
     * @param integer $max
     * @param string  $addLabel
     * @param string  $rmLabel
     */
    public function __construct($id, $label, $defaultCount, $min, $max, $addLabel, $rmLabel)
    {
        $this->id           = $id;
        $this->label        = $label;
        $this->defaultCount = $defaultCount;
        $this->min          = $min;
        $this->max          = $max;
        $this->addLabel     = $addLabel;
        $this->rmLabel      = $rmLabel;

        $fields = func_get_args();
        $fields = array_values($fields);
        foreach ($fields as $i => $field) {
            if (!$field instanceof \kabar\Widget\Widget\Fields\AbstractField) {
                unset($fields[$i]);
                continue;
            }
            $field->setSuffix('['.$i.']');
        }
        $this->fields = $fields;

        add_action('customize_controls_enqueue_scripts', array($this, 'addScripts'));
    }

    /**
     * Adds repater JS
     */
    public function addScripts()
    {
        wp_enqueue_script(
            'widget-repeater',
            $this->getAssetsUri().'js/Repeater.js',
            array(),
            ServiceLocator::VERSION,
            self::IN_FOOTER
        );
    }


    /**
     * Binds field instance to WordPress widget
     * @param WP_Widget $widget  WordPress widget instance
     */
    public function bindToWidget(\WP_Widget $widget)
    {
        $this->widgetInstance = $widget;
        foreach ($this->fields as $index => $field) {
            $field->bindToWidget($this->widgetInstance);
        }
    }

    /**
     * Returns widget field value
     * @param  array $args     Widget arguments.
     * @param  array $instance Saved values from database.
     * @return string
     */
    public function get($args, $instance)
    {
        $repeater = array();
        foreach ($instance as $fieldId => $field) {
            if (is_array($field)) {
                foreach ($field as $index => $fieldValue) {
                    $repeater[$index][$fieldId] = $fieldValue;
                }
            }
        }
        return $repeater;
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
        $template = ServiceLocator::getNew('Component', 'Template');
        $template($this->getTemplatesDirectory().$this->getFieldType().self::TEMPLATE_EXTENSION);
        $template->fieldId   = $this->getWordPressFieldId();
        $template->fieldName = $this->getWordPressFieldName();
        $template->cssClass  = $this->getCssClass();
        $template->addLabel  = $this->addLabel;
        $template->rmLabel   = $this->rmLabel;

        $template->id     = $this->id;
        $template->label  = $this->label;

        $fieldsets = array();
        $count = $this->count($instance);
        if ($count === false) {
            trigger_error('Repeater fields should provide array.', E_USER_WARNING);
        }
        for ($i = 1; $i <= $count; $i++) {
            $fieldsets[$i] = array();
            foreach ($this->fields as $index => $field) {
                $field->setSuffix('['.$i.']');
                $fieldId = $field->getId();
                $fieldInstance = array();
                $fieldInstance[$fieldId] = isset($instance[$fieldId][$i]) ? $instance[$fieldId][$i] : null;
                $fieldsets[$i][] = $field->form($fieldInstance);
            }
        }
        $template->fieldsets = $fieldsets;
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
        $fields = array();
        $count = $this->count($newInstance);
        if ($count === false) {
            trigger_error('Repeater fields should provide array.', E_USER_WARNING);
        }
        for ($i = 1; $i <= $count; $i++) {
            foreach ($this->fields as $index => $field) {
                $fieldId                    = $field->getId();
                $fieldNewInstance           = array();
                $fieldNewInstance[$fieldId] = isset($newInstance[$fieldId][$i]) ? $newInstance[$fieldId][$i] : null;
                $fieldNewInstance           = $field->update($fieldNewInstance, array());
                $newInstance[$fieldId][$i]  = $fieldNewInstance[$fieldId];
            }
        }
        return $newInstance;
    }

    /**
     * Check how many times field was repeated
     * @param  array $instance
     * @return bool
     */
    private function count($instance)
    {
        foreach ($instance as $field) {
            if (is_array($field)) {
                return count($field);
            }
        }
        return false;
    }
}
