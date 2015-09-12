<?php
/**
 * Form component
 *
 * Provides easy way of assembling forms
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Component
 */
namespace kabar\Component\Form;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Form class
 */
final class Form extends \kabar\Module\Module\Module
{

    const NONCE_SUFFIX  = '_nonce';
    const ACTION_SUFFIX = '_save';
    const GET_METHOD    = 'get';
    const POST_METHOD   = 'post';
    const SEND_TO_SELF  = 'self';

    /**
     * Form id
     * @var string
     */
    private $id;

    /**
     * Form method
     * @var string
     */
    private $method;

    /**
     * Form action
     * @var string
     */
    private $action;

    /**
     * Form fields
     * @var array
     */
    private $fields = array();

    /**
     * Form nonce field
     * @var \kabar\Utility\Fields\Nonce
     */
    private $nonce;

    /**
     * Form template
     * @var \kabar\Component\Template\Template
     */
    private $template;

    /**
     * Form storage object
     * @var \kabar\Utility\Storage\InterfaceStorage
     */
    private $storage;

    /**
     * Path to directory with fields templates. With trailing slash.
     * @var string
     */
    private $fieldsTemplateDir;

    /**
     * Reserved field names used by form
     * @since 2.32.0
     * @var array
     */
    private $reservedFieldNames = array('formNonce', 'formId', 'formMethod', 'formAction', 'formFields');

    // INTERFACE

    /**
     * Setup form
     * @param string                                       $id
     * @param string                                       $method
     * @param string                                       $action
     * @param \kabar\Utility\Storage\InterfaceStorage|null $storage
     * @param \kabar\Component\Template\Template|null      $template
     * @param string                                       $fieldsTemplateDir
     */
    public function __construct(
        $id,
        $method = '',
        $action = '',
        \kabar\Utility\Storage\InterfaceStorage $storage = null,
        \kabar\Component\Template\Template $template = null,
        $fieldsTemplateDir = ''
    ) {
        $this->id                = $id;
        $this->method            = in_array($method, array(self::GET_METHOD, self::POST_METHOD)) ? $method : self::POST_METHOD;
        $this->action            = empty($action) ? get_home_url(null, htmlspecialchars($_SERVER['PHP_SELF'])) : $action;
        $this->storage           = $storage;
        $this->template          = $template;
        $this->fieldsTemplateDir = $fieldsTemplateDir;

        $this->nonce = new \kabar\Utility\Fields\Nonce($this->id.self::NONCE_SUFFIX, $this->id.self::ACTION_SUFFIX);

        add_action('admin_enqueue_scripts', array($this, 'enqueueAssets'));
    }

    /**
     * Adds fields to form
     * @since  2.0.0
     * @param  \kabar\Utility\Fields\InterfaceFormPart ...$field
     * @return void
     */
    public function addFields()
    {
        $fields = func_get_args();
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    /**
     * Checks if form was sent and we can process it.
     * @return bool
     */
    public function sent()
    {
        return isset($this->nonce) && $this->nonce->get() === true;
    }

    /**
     * Get form data as array or return false if not sent, or nonce is invalid
     * @since  2.24.4
     * @return array|bool
     */
    public function save()
    {
        if (!$this->sent()) {
            return false;
        }
        $form = array();
        foreach ($this->fields as $slug => $field) {
            if ($field instanceof \kabar\Utility\Fields\InterfaceField) {
                $form[$field->getSlug()] = $field->save();
            }
        }
        return $form;
    }

    /**
     * Renders form content
     * @since 2.0.0
     */
    public function render()
    {
        if ($this->template instanceof \kabar\Component\Template\Template) {
            $template = $this->template;
        } else {
            $template = new \kabar\Component\Template\Template;
            $template($this->getTemplatesDirectory().'Form.php');
        }

        $template->formNonce  = $this->nonce->render();
        $template->formId     = $this->id;
        $template->formMethod = $this->method;
        $template->formAction = $this->action;

        $fields = array();
        foreach ($this->fields as $field) {
            $fields[] = $field->render();
        }
        $template->formFields   = $fields;
        $template->formCssClass = $this->getCssClass();
        return $template;
    }

    /**
     * Returns storage object, if it doesn't exists it will be created
     *
     * @since 2.0.0
     * @return \kabar\Utility\Storage\InterfaceStorage
     */
    public function getStorage()
    {
        if ($this->storage instanceof \kabar\Utility\Storage\InterfaceStorage) {
            return $this->storage;
        }

        $this->storage = new \kabar\Utility\Storage\HTTPPost();
        $this->storage->setPrefix($this->id.'-');
        return $this->storage;
    }

    /**
     * Returns template with saved form data
     * @since  2.32.0
     * @param  integer                            $id Id passed to storage object
     * @return \kabar\Component\Template\Template
     */
    public function getPopulatedTemplate($id = 0)
    {
        if ($id) {
            $storage = clone $this->getStorage();
            $storage->setId($id);
        }
        $template = new \kabar\Component\Template\Template;
        foreach ($this->fields as $field) {
            $field->setStorage($storage);
            $name            = $field->getSlug();
            $template->$name = $field->get();
            $field->setStorage($this->getStorage());
        }
        return $template;
    }

    // INTERNAL

    /**
     * Adds field to form
     * @since 2.0.0
     * @param \kabar\Utility\Fields\InterfaceFormPart $field
     */
    private function addField(\kabar\Utility\Fields\InterfaceFormPart $field)
    {
        if ($field instanceof \kabar\Utility\Fields\InterfaceField && !$field->hasStorage()) {
            $field->setStorage($this->getStorage());
        }
        $slug = $field->getSlug();
        if (in_array($slug, $this->reservedFieldNames)) {
            trigger_error('Field name '.$slug.' is reserved by Form component!', E_USER_ERROR);
        }
        $field->setTemplateDirectory($this->fieldsTemplateDir);

        $this->fields[$slug] = $field;
    }

    /**
     * Equeue assets
     * @access private
     * @since  2.24.4
     * @return void
     */
    public function enqueueAssets()
    {
        wp_enqueue_style(
            $this->getModuleSlug().'-style',
            $this->getAssetsUri().'css/Fields.css',
            array(),
            $this->getLibraryVersion(),
            'all'
        );
    }
}
