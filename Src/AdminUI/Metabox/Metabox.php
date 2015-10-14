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

namespace kabar\AdminUI\Metabox;

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
     * Form for this metabox
     * @var \kabar\Utility\Form\Form
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
     */
    public function __construct(
        $id,
        $title,
        $screen = 'post',
        $context = 'normal',
        $priority = 'high'
    ) {
        $this->id                = $id;
        $this->title             = $title;
        $this->screens           = is_array($screen) ? $screen : array($screen);
        $this->context           = $context;
        $this->priority          = $priority;

        // form
        $storage = new \kabar\Utility\Storage\PostMeta();
        $storage->setPrefix($id.'-');

        $this->form = new \kabar\Utility\Form\Form(
            $this->id,
            '',
            '',
            $storage
        );

        add_action('add_meta_boxes', array($this, 'add'));
        add_action('save_post', array($this, 'update'));
    }

    /**
     * Get component/form id
     * @since  2.39.0
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns metabox form
     * @since  2.24.4
     * @return \kabar\Utility\Form\Form
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
        if ($postId) {
            return $this->form->getField($setting)->getForId($postId);
        }
        return $this->form->getField($setting)->get();
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
}
