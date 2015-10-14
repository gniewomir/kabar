<?php
/**
 * Fieldset class
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage fields
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Fieldset class
 */
class Fieldset extends AbstractField implements InterfaceFieldset
{

    /**
     * Field slug
     * @var string
     */
    protected $slug;

    /**
     * Field title
     * @var string
     */
    protected $title;

    /**
     * Fieldset options
     * @var array
     */
    protected $options;

    /**
     * Fieldset fields
     * @var array
     */
    protected $fields;

    /**
     * Fieldset storage shared with fields
     * @var \kabar\Utility\Storage\InterfaceStorage
     */
    protected $storage;

    /**
     * Setup fieldset
     * @param string $slug
     * @param string $title
     * @param array  $options
     * @param \kabar\Utility\Fields\InterfaceFormPart ...$field
     */
    public function __construct($slug, $title, $options)
    {
        $this->slug    = $slug;
        $this->title   = empty($title) ? '' : $title;
        $this->options = $options;

        $fields = func_get_args();
        array_shift($fields);
        array_shift($fields);
        array_shift($fields);

        foreach ($fields as $field) {
            if (!$field instanceof \kabar\Utility\Fields\InterfaceFormPart) {
                trigger_error('Passed argument is not implementing "InterfaceFormPart" interface', E_USER_ERROR);
                continue;
            }
            $this->fields[$field->getSlug()] = $field;
        }
    }

    /**
     * Binds storage object to fieldset and its fields
     * @param \kabar\Utility\Storage\InterfaceStorage $storage
     */
    public function setStorage(\kabar\Utility\Storage\InterfaceStorage $storage)
    {
        $this->storage = $storage;
        foreach ($this->fields as $field) {
            if ($field instanceof \kabar\Utility\Fields\InterfaceField) {
                $field->setStorage($this->storage);
            }
        }
    }

    /**
     * Get field value
     * @return string
     */
    public function get()
    {
        return $this->fields;
    }

    /**
     * Render fields
     * @return \kabar\Utility\Template\Template
     */
    public function render()
    {
        $template           = $this->getTemplate();
        $template->id       = $this->storage->getPrefixedKey($this->getSlug());
        $template->cssClass = $this->getCssClass();
        $template->title    = $this->title;
        $fields = array();
        foreach ($this->fields as $field) {
            $fields[] = $field->render();
        }
        $template->fields = $fields;
        return $template;
    }

    /**
     * Save fields
     * @return array
     */
    public function save()
    {
        $values = array();
        // save all fields
        foreach ($this->fields as $field) {
            if ($field instanceof \kabar\Utility\Fields\InterfaceField) {
                $values[$field->getSlug()] = $field->save();
            }
        }
        return $values;
    }
}
