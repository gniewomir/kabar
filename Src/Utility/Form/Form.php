<?php
/**
 * Form component
 *
 * @since      2.0.0
 * @package    kabar
 * @subpackage component
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Utility\Form;

/**
 * Provides easy way of assembling forms from field objects
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
     * Form template object
     * @var \kabar\Utility\Template\Template
     */
    private $template;

    /**
     * Form storage object
     * @var \kabar\Utility\Storage\InterfaceStorage
     */
    private $storage;

    /**
     * Fields templates subdirectory name
     * @var string
     */
    private $fieldsTemplateDir;

    /**
     * Callbacks to run when form is saved
     * @since 2.35.0
     * @var array<callable>
     */
    private $updateCallbacks = array();

    /**
     * Reserved field names used by form
     * @since 2.32.0
     * @var array
     */
    private $reservedFieldNames = array('formNonce', 'formId', 'formMethod', 'formAction', 'formFields');

    // INTERFACE

    /**
     * Setup form
     * @param string                                       $id                ID
     * @param string                                       $method            Method. POST if not specified
     * @param string                                       $action            Action. SELF if not specified
     * @param \kabar\Utility\Storage\InterfaceStorage|null $storage           Storage object. \kabar\Utility\Storage\HTTPPost if not specified
     * @param \kabar\Utility\Template\Template|null      $template          Template object. Default template if not specified
     * @param string                                       $fieldsTemplateDir Fields templates subdirectory. "Default" if not specifed
     * @param callable|null                                $updateCallback    Callback to run when form is saved
     */
    public function __construct(
        $id,
        $method = '',
        $action = '',
        \kabar\Utility\Storage\InterfaceStorage $storage = null,
        \kabar\Utility\Template\Template $template = null,
        $fieldsTemplateDir = 'Default',
        callable $updateCallback = null
    ) {
        $this->id                = $id;
        $this->method            = in_array($method, array(self::GET_METHOD, self::POST_METHOD)) ? $method : self::POST_METHOD;
        $this->action            = empty($action) ? get_home_url(null, htmlspecialchars($_SERVER['PHP_SELF'])) : $action;
        $this->storage           = $storage;
        $this->template          = $template;
        $this->fieldsTemplateDir = $fieldsTemplateDir;

        if ($updateCallback) {
            $this->updateCallbacks[] = $updateCallback;
        }

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
     * Return field
     * @since  2.37.2
     * @param  string $slug
     * @return \kabar\Utility\Fields\InterfaceField
     */
    public function getField($slug)
    {
        return $this->fields[$slug];
    }

    /**
     * Add update callback
     * @since 2.35.0
     * @param callable $updateCallback
     */
    public function addUpdateCallback(callable $updateCallback)
    {
        $this->updateCallbacks[] = $updateCallback;
    }

    /**
     * Checks if form was sent and we can process it.
     *
     * Method assumes, that form was sent if form nonce is present and valid
     *
     * @return bool
     */
    public function sent()
    {
        return isset($this->nonce) && $this->nonce->get() === true;
    }

    /**
     * Save form fields, run registered update callbacks and return data as array
     * @since  2.24.4
     * @return array|false
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
        foreach ($this->updateCallbacks as $callback) {
            call_user_func_array($callback, array($this));
        }
        return $form;
    }

    /**
     * Renders form content
     * @since 2.0.0
     */
    public function render()
    {
        if ($this->template instanceof \kabar\Utility\Template\Template) {
            $template = $this->template;
        } else {
            $template = new \kabar\Utility\Template\Template;
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
        $this->storage->setPrefix($this->id);
        return $this->storage;
    }

    /**
     * Returns form template with saved form data
     * @since  2.32.0
     * @param  integer                            $id Id passed to storage object
     * @return \kabar\Utility\Template\Template
     */
    public function getPopulatedTemplate($id = 0)
    {
        $template = new \kabar\Utility\Template\Template();

        // no id provided
        if (!$id) {
            foreach ($this->fields as $field) {
                if ($field instanceof \kabar\Utility\Fields\InterfaceField) {
                    $name            = $field->getSlug();
                    $template->$name = $field->get();
                }
            }
            return $template;
        }

        // id provided
        foreach ($this->fields as $field) {
            if (!$field instanceof \kabar\Utility\Fields\InterfaceField) {
                continue;
            }
            $copy = clone $field;
            $copy->getStorage()->setId($id);
            $name            = $copy->getSlug();
            $template->$name = $copy->get();
        }
        return $template;
    }

    // INTERNAL

    /**
     * Adds field to form
     * @since  2.0.0
     * @param  \kabar\Utility\Fields\InterfaceFormPart $field
     * @return void
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
        if ($this->fieldsTemplateDir) {
            $field->setTemplateDirectory($this->fieldsTemplateDir);
        }
        $this->fields[$slug] = $field;
    }

    /**
     * Equeue assets
     * @internal
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
