<?php
/**
 * Metabox component
 *
 * @package    kabar
 * @subpackage component
 * @since      2.0.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Component\Metabox;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Registers metabox with WordPress and allows interacting with metabox form object
 */
final class Metabox extends \kabar\Module\Module\Module
{
    /**
     * Metabox ID
     * @var   string
     */
    private $id;

    /**
     * Metabox title
     * @var   string
     */
    private $title;

    /**
     * Locations where to add this metabox
     * @var   array
     * @see   https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    private $screens;

    /**
     * The part of the page where the edit screen section should be show
     * @var string
     * @see https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    private $context;

    /**
     * The priority within the context where the boxes should show
     * @var string
     * @see https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    private $priority;

    /**
     * Template object for metabox form
     * @var \kabar\Component\Template\Template
     */
    private $template;

    /**
     * Storage object for metabox form fields
     * @var \kabar\Utility\Storage\InterfaceStorage
     */
    private $storage;

    /**
     * Fields templates subdirectory name
     * @var string
     */
    private $fieldsTemplateDir;

    /**
     * Form for this metabox
     * @var \kabar\Component\Form\Form
     */
    private $form;

    // INTERFACE

    /**
     * Setup metabox
     *
     * Arguments $id, $title, $screen, $context, $priority are passed to WordPress 'add_meta_box' function.
     * @see   https://codex.wordpress.org/Function_Reference/add_meta_box
     *
     * @param string                                       $id
     * @param string                                       $title
     * @param string|array<string>                         $screen
     * @param string                                       $context
     * @param string                                       $priority
     * @param \kabar\Utility\Storage\InterfaceStorage|null $storage           Storage object for metabox form fields
     * @param \kabar\Component\Template\Template|null      $template          Template for metabox form
     * @param string                                       $fieldsTemplateDir Fields templates subdirectory name
     */
    public function __construct(
        $id,
        $title,
        $screen = 'post',
        $context = 'normal',
        $priority = 'high',
        \kabar\Utility\Storage\InterfaceStorage $storage = null,
        \kabar\Component\Template\Template $template = null,
        $fieldsTemplateDir = ''
    ) {
        $this->id                = $id;
        $this->title             = $title;
        $this->screens           = is_array($screen) ? $screen : array($screen);
        $this->context           = $context;
        $this->priority          = $priority;
        $this->storage           = $storage;
        $this->template          = $template;
        $this->fieldsTemplateDir = $fieldsTemplateDir;

        $this->form = new \kabar\Component\Form\Form(
            $this->id,
            '',
            '',
            $this->getStorage(),
            $this->template,
            $this->fieldsTemplateDir
        );

        add_action('add_meta_boxes', array($this, 'add'));
        add_action('save_post', array($this, 'update'));
    }

    /**
     * Returns metabox form
     * @since  2.24.4
     * @return \kabar\Component\Form\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Return metabox setting for particular post
     * @since  2.24.4
     * @param  string      $setting
     * @param  integer     $postId
     * @return string|null
     */
    public function getSetting($setting, $postId = 0)
    {
        $field = clone $this->form->getField($setting);
        if ($postId) {
            $field->getStorage()->setId($postId);
        }
        return $field->get();
    }

    // INTERNAL

    /**
     * WordPress action 'add_meta_boxes', add metabox to all specified screens/custom post types
     * @internal
     * @see   https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    public function add()
    {
        foreach ($this->screens as $screen) {
            add_meta_box($this->id, $this->title, array($this, 'render'), $screen, $this->context, $this->priority);
        }
    }

    /**
     * Callback for WordPress. Renders metabox content
     * @internal
     * @param  \WP_Post $post
     * @param  array    $metabox
     * @see    https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    public function render($post, $metabox)
    {
        echo $this->form->render();
    }

    /**
     * WordPress action 'save_post'. Save all metabox fields on post save
     * @internal
     * @param int $postId The post ID.
     */
    public function update($postId)
    {
        // Bail if autosave or post revision
        if (wp_is_post_autosave($postId) || wp_is_post_revision($postId)) {
            return;
        }

        // If there is no nonce or it is invalid we do nothing
        if (!$this->form->sent()) {
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

        $this->form->save();
    }

    /**
     * Returns storage object, if it doesn't exists it will be created
     * @return \kabar\Utility\Storage\InterfaceStorage
     */
    private function getStorage()
    {
        if ($this->storage) {
            return $this->storage;
        }
        $this->storage = new \kabar\Utility\Storage\PostMeta;
        $this->storage->setPrefix($this->id.'-');
        return $this->storage;
    }
}
