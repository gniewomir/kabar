<?php

namespace kabar\Widget\Widget;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * FieldsCollection API allowing to easy building complicated widgets.
 *
 * @internal
 * @see        \kabar\Widget\Widget\WordPressWidget
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage Widgets
 */
class FieldsCollection
{
    /**
     * Fields collection
     * @var array
     */
    private $fields = array();

    /**
     * Widget arguments
     *
     * Set when renderFields method is called
     *
     * @var array
     */
    private $args;

    /**
     * Widget instance data
     *
     * Set when renderFields method is called
     *
     * @var array
     */
    private $instance;

    /**
     * WordPress widget instance
     * @var \WP_Widget
     */
    private $widget;

    /**
     * This widget instance template
     * @var \kabar\Component\Template\Template
     */
    private $template;

    /**
     * Create individual fields instaces
     * @param WP_Widget $widget WordPress widget instance
     */
    public function __construct(\WP_Widget $widget)
    {
        $this->widget = $widget;
    }

    /**
     * Add field to collection
     * @since 2.0.0
     * @param \kabar\Widget\Widget\Fields\AbstractField $field
     */
    public function addField(\kabar\Widget\Widget\Fields\AbstractField $field)
    {
        $field->bindToWidget($this->widget);
        $id                = $field->getId();
        $this->fields[$id] = $field;
    }

    /**
     * Add fields to collection
     * @since 2.0.0
     * @param \kabar\Widget\Widget\Fields\AbstractField $firstField First of multiple possibile fields instances
     */
    public function addFields(\kabar\Widget\Widget\Fields\AbstractField $firstField)
    {
        $fields = func_get_args();
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    /**
     * Remove already added field
     * @param  string $fieldId
     * @return void
     */
    public function removeField($fieldId)
    {
        if (method_exists($this->fields[$fieldId], 'remove')) {
            $this->fields[$fieldId]->remove();
        }
        unset($this->fields[$fieldId]);
    }

    /**
     * Add field after field with specified id
     * @since 2.0.0
     * @param string                                    $fieldId Existing field ID
     * @param \kabar\Widget\Widget\Fields\AbstractField $field   Field object
     * @return void
     */
    public function insertFieldAfter($fieldId, $field)
    {
        if (!array_key_exists($fieldId, $this->fields)) {
            trigger_error(sprintf('Cannot add field after "%s". Field doesn\'t exists', $fieldId), E_USER_WARNING);
        }

        $new = array();
        foreach ($this->fields as $id => $alreadyAddedField) {
            $new[$id] = $alreadyAddedField;
            if ($id === $fieldId) {
                $field->bindToWidget($this->widget);
                $newId       = $field->getId();
                $new[$newId] = $field;
            }
        }
        $this->fields = $new;
    }

    /**
     * Add fields after field with specified id
     * @since 2.0.0
     * @param string                                    $fieldId    Existing field ID
     * @param \kabar\Widget\Widget\Fields\AbstractField $firstField This, and every other argument will be treated as field object
     * @return void
     */
    public function insertFieldsAfter($fieldId, \kabar\Widget\Widget\Fields\AbstractField $firstField)
    {
        if (!array_key_exists($fieldId, $this->fields)) {
            trigger_error(sprintf('Cannot add fields after "%s". Field doesn\'t exists', $fieldId), E_USER_WARNING);
        }

        $fields = func_get_args();
        array_shift($fields); // strip $fieldId

        $new = array();
        foreach ($this->fields as $id => $alreadyAddedField) {
            $new[$id] = $alreadyAddedField;
            if ($id === $fieldId) {
                foreach ($fields as $field) {
                    $field->bindToWidget($this->widget);
                    $newId       = $field->getId();
                    $new[$newId] = $field;
                }
            }
        }
        $this->fields = $new;
    }

    /**
     * Add field before field with specified id
     * @since 2.0.0
     * @param string                                    $fieldId Existing field ID
     * @param \kabar\Widget\Widget\Fields\AbstractField $field   Field object
     * @return void
     */
    public function insertFieldBefore($fieldId, $field)
    {
        if (!array_key_exists($fieldId, $this->fields)) {
            trigger_error(sprintf('Field "%s" don\'t exist.', $fieldId), E_USER_WARNING);
            return;
        }

        $fields = func_get_args();
        array_shift($fields); // strip $fieldId

        // add before first field in collection
        reset($this->fields);
        if (key($this->fields) == $fieldId) {
            $indexedField[$field->getId()] = $field;
            $this->fields = array_merge($indexedField, $this->fields);

            return;
        }

        // add before any other field in collection
        while (key($this->fields) !== $fieldId) {
            next($this->fields);
        }
        prev($this->fields);
        $key = key($this->fields);

        // add after key previous to $fieldId
        call_user_func_array(array($this, 'insertFieldAfter'), array($key, $field));
    }

    /**
     * Add fields before field with specified id
     * @since 2.0.0
     * @param string                                    $fieldId    Existing field ID
     * @param \kabar\Widget\Widget\Fields\AbstractField $firstField This, and every other argument will be treated as field object
     */
    public function insertFieldsBefore($fieldId, \kabar\Widget\Widget\Fields\AbstractField $firstField)
    {

        if (!array_key_exists($fieldId, $this->fields)) {
            trigger_error(sprintf('Field "%s" don\'t exist.', $fieldId), E_USER_WARNING);
            return;
        }

        $fields = func_get_args();
        array_shift($fields); // strip $fieldId

        // add before first field in collection
        reset($this->fields);
        if (key($this->fields) == $fieldId) {
            $indexedFields = array();
            foreach ($fields as $key => $field) {
                $indexedFields[$field->getId()] = $field;

            }
            $this->fields = array_merge($indexedFields, $this->fields);

            return;
        }

        // add before any other field in collection
        while (key($this->fields) !== $fieldId) {
            next($this->fields);
        }
        prev($this->fields);
        $key = key($this->fields);

        // add after key previous to $fieldId
        call_user_func_array(array($this, 'insertFieldsAfter'), array_merge(array($key), $fields));
    }

    /**
     * Setup fields values in template
     *
     * @param  array                              $args     Widget arguments.
     * @param  array                              $instance Saved values from database.
     * @param  \kabar\Component\Template\Template $template Template object
     * @return \kabar\Component\Template\Template
     */
    public function populateTemplate($args, $instance, \kabar\Component\Template\Template $template)
    {
        $this->args     = $args;     // required by get method
        $this->instance = $instance; // required by get method
        foreach ($this->fields as $key => $field) {
            $template->$key = $this->fields[$key]->get($this->args, $this->instance);
        }

        // pass widget id to template
        $template->widgetId = $this->args['widget_id'];

        return $template;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $newInstance Values just sent to be saved.
     * @param array $oldInstance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function updateFields($newInstance, $oldInstance)
    {
        foreach ($this->fields as $field) {
            $newInstance = $field->update($newInstance, $oldInstance);
        }

        return $newInstance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function render($instance)
    {
        foreach ($this->fields as $field) {
            echo $field->form($instance);
        }
    }

    /**
     * Get field value to render it in template
     * @param  string $id Field id
     * @return mixed
     */
    private function get($id)
    {
        if (isset($this->fields[$id])) {
            return $this->fields[$id]->get($this->args, $this->instance);
        }

        trigger_error('No field with id: '.$id, E_USER_WARNING);
    }
}
