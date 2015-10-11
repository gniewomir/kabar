<?php
/**
 * User profile module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.24.4
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\UserProfile;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * UserProfile module main class
 */
final class UserProfile extends \kabar\Module\Module\Module
{
    const IN_FOOTER = true;
    const SINGLE    = true;

    /**
     * User prifile sections and fields
     * @var array
     */
    private $sections;

    // INTERFACE

    /**
     * Setup user profile
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueAssets'));

        add_action('show_user_profile', array($this, 'profile'));
        add_action('edit_user_profile', array($this, 'profile'));
        add_action('personal_options_update', array($this, 'update'));
        add_action('edit_user_profile_update', array($this, 'update'));
    }

    /**
     * Add settings section to user profile
     * @param string $id
     * @param string $title
     * @param string $position
     */
    public function addSection($id, $title, $position = 'bottom')
    {
        $storage                     = new \kabar\Utility\Storage\UserMeta();
        $storage->setPrefix($id.'-');
        $template                    = new \kabar\Component\Template\Template();
        $template($this->getTemplatesDirectory().'Section.php');
        $template->title             = $title;
        $template->containerCssClass = $position == 'bottom' ? 'section-postion-bottom' : 'section-position-top';
        $this->sections[$id]         = new \kabar\Component\Form\Form(
            $id,
            '',
            '',
            $storage,
            $template,
            'Table'
        );
    }

    /**
     * Returns section form object, which allows adding and removing fields
     * @param  string                     $id
     * @return \kabar\Component\Form\Form
     */
    public function getSectionForm($id)
    {
        return $this->sections[$id];
    }

    /**
     * Get user setting
     * @see https://codex.wordpress.org/Function_Reference/get_user_meta
     * @param  string $userId
     * @param  string $sectionId
     * @param  string $setting
     * @return string
     */
    public function getSetting($userId, $sectionId, $setting)
    {
        $field = clone $this->sections[$sectionId]->getField($setting);
        $field->getStorage()->setId($userId);
        return $field->get();
    }

    // INTERNAL

    /**
     * Enqueue assets
     * @access private
     */
    public function enqueueAssets()
    {
        wp_enqueue_script(
            $this->getModuleSlug(),
            $this->getAssetsUri().'js/UserProfile.js',
            array(),
            $this->getLibraryVersion(),
            self::IN_FOOTER
        );
    }

    /**
     * WordPress action. Show additional user profile fields
     * @access private
     * @param  \WP_User $user
     * @return void
     */
    public function profile($user)
    {
        if (empty($this->sections)) {
            return;
        }
        foreach ($this->sections as $id => $form) {
            echo $form->render();
        }
    }

    /**
     * WordPress action. Update user profile
     * @access private
     * @param  int $userId
     * @return void
     */
    public function update($userId)
    {
        foreach ($this->sections as $id => $form) {
            if ($form->sent()) {
                $form->save();
            }
        }
    }
}
