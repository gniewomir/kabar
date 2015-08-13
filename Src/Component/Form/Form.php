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
class Form extends \kabar\Module\Module\Module
{

    const NONCE_SUFFIX  = '_nonce';
    const ACTION_SUFFIX = '_save';

    const GET_METHOD    = 'get';
    const POST_METHOD   = 'post';

    /**
     * Form id
     * @var string
     */
    protected $id;

    /**
     * Form id
     * @var string
     */
    protected $name;

    /**
     * Form id
     * @var string
     */
    protected $method;

    /**
     * Form id
     * @var string
     */
    protected $action;

    /**
     * Form fields
     * @var array
     */
    protected $fields = array();

    /**
     * Form nonce field
     * @var \kabar\Utility\Fields\Nonce
     */
    protected $nonce;

    /**
     * Form storage object
     * @var \kabar\Utility\Storage\InterfaceStorage
     */
    protected $storage;

    /**
     * Setup form
     * @param string $id
     */
    public function __construct($id, $method = '', $action = '')
    {
        $this->id     = $id;
        $this->name   = $id;
        $this->method = in_array($method, array(self::GET_METHOD, self::POST_METHOD)) ? $method : self::POST_METHOD;

        if (empty($action)) {
            $action = get_home_url(null, $_SERVER['REQUEST_URI']);
        }

        $this->action = htmlspecialchars($action);
        $this->nonce  = new \kabar\Utility\Fields\Nonce($this->id.self::NONCE_SUFFIX, $this->id.self::ACTION_SUFFIX);
    }

    /**
     * Returns form id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Adds field to form
     * @since 2.0.0
     * @param \kabar\Utility\Fields\InterfaceFormPart $field
     */
    public function addField(\kabar\Utility\Fields\InterfaceFormPart $field)
    {
        if ($field instanceof \kabar\Utility\Fields\InterfaceField) {
            $field->setStorage($this->getStorage());
        }
        $slug                = $field->getSlug();
        $this->fields[$slug] = $field;
    }

    /**
     * Adds fields to form
     * @since 2.0.0
     * @param \kabar\Utility\Fields\InterfaceFormPart $firstField First of multiple possibile fields instances
     */
    public function addFields(\kabar\Utility\Fields\InterfaceFormPart $firstField)
    {
        $fields = func_get_args();
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    /**
     * Renders form content
     * @since 2.0.0
     * @param \WP_Post $post
     * @param array    $metabox
     */
    public function render()
    {
        $template = ServiceLocator::getNew('Component', 'Template');
        $template(__DIR__.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'Form.php');
        $template->nonce  = $this->nonce->render();
        $template->id     = $this->id;
        $template->name   = $this->name;
        $template->method = $this->method;
        $template->action = $this->action;
        $fields = array();
        foreach ($this->fields as $field) {
            $fields[] = $field->render();
        }
        $template->fields   = $fields;
        $template->cssClass = $this->getCssClass();
        return $template;
    }

    /**
     * Checks if form was sent and we can process it.
     * @return bool
     */
    public function sent()
    {
        return $this->checkNonce();
    }

    /**
     * Get form data as array or return false if
     * @since  2.0.0
     * @return array|bool
     */
    public function getFormAsArray()
    {
        if (!$this->sent()) {
            return false;
        }

        $form = array();
        foreach ($this->fields as $slug => $field) {
            if ($field instanceof \kabar\Utility\Fields\InterfaceFieldset) {
                $fieldsetFields = $field->get();
                foreach ($fieldsetFields as $fieldsetField) {
                    $form[$fieldsetField->getSlug()] = $fieldsetField->save();
                }
            } else if ($field instanceof \kabar\Utility\Fields\InterfaceField) {
                $form[$field->getSlug()] = $field->save();
            }
        }

        return $form;
    }

    /**
     * Checks if nonce is set and valid
     * @since 2.0.0
     * @return bool
     */
    protected function checkNonce()
    {
        return isset($this->nonce) && $this->nonce->get() === true;
    }

    /**
     * Returns storage object, if it doesn't exists it will be created
     *
     * We want to create storage as late as we can, because it needs to determine current post id. So we instantiat it JIT.
     *
     * @since 2.0.0
     * @return \kabar\Utility\Storage\PostMeta
     */
    protected function getStorage()
    {
        if (!$this->storage instanceof InterfaceStorage) {
            $this->storage = new \kabar\Utility\Storage\HTTPPost;
            $this->storage->setPrefix($this->id.'-');
        }

        return $this->storage;
    }
}
