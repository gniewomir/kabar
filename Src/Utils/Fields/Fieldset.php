<?php
/**
 * Fieldset class
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */

namespace kabar\Utils\Fields;

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
     * Field template file path
     * @var string
     */
    protected $template;

    /**
     * Fieldset fields
     * @var array
     */
    protected $fields;

    /**
     * Fieldset storage shared with fields
     * @var \kabar\Utils\Fields\Storage
     */
    protected $storage;

    /**
     * Setup fieldset
     * @param string $slug
     * @param string $title
     * @param array  $options
     * @param \kabar\Utils\Fields\InterfaceFormPart $firstOfManyField
     */
    public function __construct($slug, $title, $options, \kabar\Utils\Fields\InterfaceFormPart $firstOfManyField)
    {
        $this->slug    = $slug;
        $this->title   = empty($title) ? '' : $title;
        $this->options = $options;

        $fields = func_get_args();
        array_shift($fields);
        array_shift($fields);
        array_shift($fields);

        foreach ($fields as $field) {
            if (!$field instanceof \kabar\Utils\Fields\InterfaceFormPart) {
                trigger_error('Passed argument is not implementing "InterfaceFormPart" interface', E_USER_ERROR);
                continue;
            }
            $this->fields[$field->getSlug()] = $field;
        }
        $this->template = $this->getTemplatesDir().'Fieldset.php';
    }

    /**
     * Binds storage object to fieldset and its fields
     * @param \kabar\Utils\Storage\InterfaceStorage $storage
     */
    public function setStorage(\kabar\Utils\Storage\InterfaceStorage $storage)
    {
        $this->storage = $storage;
        foreach ($this->fields as $field) {
            if ($field instanceof \kabar\Utils\Fields\InterfaceField) {
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
     * @return /kabar/Component/Template/Template
     */
    public function render()
    {
        $template = ServiceLocator::getNew('Component', 'Template');
        $template($this->template);
        $template->id       = $this->storage->getFieldId($this->getSlug());
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
     * @return string
     */
    public function save()
    {
        // save all fields
        foreach ($this->fields as $field) {
            if ($field instanceof \kabar\Utils\Fields\InterfaceField) {
                $field->save();
            }
        }
    }
}
