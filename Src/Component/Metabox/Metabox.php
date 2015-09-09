<?php
/**
 * Metabox module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Component
 */

namespace kabar\Component\Metabox;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Registers and provides API for single metabox
 */
final class Metabox extends \kabar\Module\Module\Module
{
    const SINGLE = true;

    /**
     * Metabox ID
     * @since 2.0.0
     * @var   string
     */
    private $id;

    /**
     * Metabox title
     * @since 2.0.0
     * @var   string
     */
    private $title;

    /**
     * Locations where to add this metabox
     * @since 2.0.0
     * @var   array
     * @see   https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    private $screens;

    /**
     * The part of the page where the edit screen section should be show
     * @since 2.0.0
     * @var string
     * @see https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    private $context;

    /**
     * The priority within the context where the boxes should show
     * @since 2.0.0
     * @var string
     * @see https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    private $priority;

    /**
     * Template
     * @var \kabar\Component\Template\Template
     */
    private $template;

    /**
     * Storage object
     * @var \kabar\Utility\Storage\InterfaceStorage
     */
    private $storage;

    /**
     * Path to directory with fields templates. With trailing slash.
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
     * Prepare to add metabox on proper WordPress action
     * @since 2.0.0
     * @param string               $id
     * @param string               $title
     * @param string|array<string> $screen
     * @param string               $context
     * @param string               $priority
     * @see   https://codex.wordpress.org/Function_Reference/add_meta_box
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
            $id,
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
     * @return string|null
     */
    public function getSetting($setting)
    {
        return $this->getStorage()->retrieve($setting);
    }

    // INTERNAL

    /**
     * WordPress action, add metabox to all specified screens/custom post types
     * @access private
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
     * Callback for WordPress. Renders metabox content
     * @access private
     * @since  2.0.0
     * @param  \WP_Post $post
     * @param  array    $metabox
     * @see    https://codex.wordpress.org/Function_Reference/add_meta_box
     */
    public function render($post, $metabox)
    {
        echo $this->form->render();
    }

    /**
     * WordPress action. Save all metabox fields on post save
     * @access private
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
     * Return user meta settins prefix
     * @return string
     */
    private function getSettingsPrefix()
    {
        return $this->id.'-';
    }

    /**
     * Returns storage object, if it doesn't exists it will be created
     *
     * @since 2.0.0
     * @return \kabar\Utility\Storage\InterfaceStorage
     */
    private function getStorage()
    {
        if ($this->storage instanceof \kabar\Utility\Storage\InterfaceStorage) {
            return $this->storage;
        }
        $this->storage = new \kabar\Utility\Storage\PostMeta;
        $this->storage->setPrefix($this->getSettingsPrefix());
        return $this->storage;
    }
}
