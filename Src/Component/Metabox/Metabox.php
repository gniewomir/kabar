<?php
/**
 * Metabox module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Component
 * @see        https://codex.wordpress.org/Function_Reference/add_meta_box
 */

namespace kabar\Component\Metabox;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Registers and provides API for single metabox
 */
class Metabox extends \kabar\Module\Module\Module
{

    const NONCE_SUFFIX  = '_nonce';
    const ACTION_SUFFIX = '_save';

    /**
     * Fileds contained in this metabox
     * @since 2.0.0
     * @var   array
     */
    protected $fields = array();

    /**
     * Metabox ID
     * @since 2.0.0
     * @var   string
     */
    protected $id;

    /**
     * Metabox title
     * @since 2.0.0
     * @var   string
     */
    protected $title;

    /**
     * Locations where to add this metabox
     * @since 2.0.0
     * @var   array
     * @see   https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    protected $screens;

    /**
     * The part of the page where the edit screen section should be show
     * @since 2.0.0
     * @var string
     * @see https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    protected $context;

    /**
     * The priority within the context where the boxes should show
     * @since 2.0.0
     * @var string
     * @see https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    protected $priority;

    /**
     * Metabox storage object
     * @var \kabar\Utility\Storage\InterfaceStorage
     */
    protected $storage;

    /**
     * Prepare to add metabox on proper WordPress action
     * @since 2.0.0
     * @param string $id
     * @param string $title
     * @param array  $screen
     * @param string $context
     * @param string $priority
     * @see   https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    public function __construct($id, $title, $screen = 'post', $context = 'normal', $priority = 'high')
    {
        ServiceLocator::get('Module', 'Fields');

        $this->id       = $id;
        $this->title    = $title;
        $this->screens  = is_array($screen) ? $screen : array($screen);
        $this->context  = $context;
        $this->priority = $priority;
        $this->nonce    = new \kabar\Utility\Fields\Nonce($this->id.self::NONCE_SUFFIX, $this->id.self::ACTION_SUFFIX);

        add_action('add_meta_boxes', array($this, 'add'));
        add_action('save_post', array($this, 'update'));
    }

    /**
     * Returns metabox id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * WordPress action, add metabox to all specified screens/custom post types
     * @since 2.0.0
     * @see   https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    public function add()
    {
        foreach ($this->screens as $screen) {
            add_meta_box($this->id, $this->title, array($this, 'render'), $screen, $this->context, $this->priority);
        }
    }

    /**
     * Remove THIS metabox from all specified screens/custom post types
     * @since 2.0.0
     * @see   https://codex.wordpress.org/Function_Reference/remove_meta_box
     */
    public function remove()
    {
        remove_action('add_meta_boxes', array($this, 'add'), 999);
        remove_action('save_post', array($this, 'update'));
        foreach ($this->screens as $screen) {
            remove_meta_box($this->id, $screen, $this->context);
        }
        foreach ($fields as $field) {
            if (method_exists($field, 'remove')) {
                $field->remove();
            }
        }
    }

    /**
     * Adds field to metabox
     * @since 2.0.0
     * @param \kabar\Utility\Fields\InterfaceField $field
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
     * Adds fields to metabox
     * @since 2.0.0
     * @param \kabar\Utility\Fields\InterfaceField $firstField First of multiple possibile fields instances
     */
    public function addFields(\kabar\Utility\Fields\InterfaceField $firstField)
    {
        $fields = func_get_args();
        foreach ($fields as $field) {
            $this->addField($field);
        }
    }

    /**
     * Removes field form metabox
     * @since 2.0.0
     * @param string $slug Field slug
     */
    public function removeField($slug)
    {
        unset($this->fields[$slug]);
    }

    /**
     * Returns field value
     * @param  string $slug
     * @return mixed
     */
    public function getField($slug)
    {
        return $this->fields[$slug];
    }

    /**
     * Renders metabox content
     * @since 2.0.0
     * @param \WP_Post $post
     * @param array    $metabox
     * @see   https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    public function render($post, $metabox)
    {
        echo $this->nonce->render();
        foreach ($this->fields as $field) {
            echo $field->render();
        }
    }

    /**
     * WordPress action. Save all metabox fields on post save
     * @since 2.0.0
     * @param int $postId The post ID.
     */
    public function update($postId)
    {
        // Bail if autosave or post revision
        if (wp_is_post_autosave($postId) || wp_is_post_revision($postId)) {
            return;
        }

        // If there is no nonce or it is invalid we do nothing
        if (!$this->checkNonce()) {
            return;
        }

        // Check the user's permissions.
        if (isset($_POST['post_type']) && $_POST['post_type'] == 'page') {
            if (!current_user_can('edit_page', $postId)) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $postId)) {
                return;
            }
        }

        // save all fields
        foreach ($this->fields as $field) {
            if ($field instanceof \kabar\Utility\Fields\InterfaceField) {
                $field->save();
            }
        }
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
     * Set storage object for fields
     * @since 2.15.0
     * @param \kabar\Utility\Storage\InterfaceStorage $storage
     */
    public function setStorage(\kabar\Utility\Storage\InterfaceStorage $storage)
    {
        if ($this->storage instanceof \kabar\Utility\Storage\InterfaceStorage) {
            trigger_error('Metabox storage already set!', E_USER_ERROR);
        }
        $this->storage = $storage;
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
        if (!$this->storage instanceof \kabar\Utility\Storage\InterfaceStorage) {
            $this->storage = new \kabar\Utility\Storage\PostMeta($this->id.'-');
        }

        return $this->storage;
    }
}
