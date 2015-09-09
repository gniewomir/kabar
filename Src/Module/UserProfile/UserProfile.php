<?php
/**
 * User profile module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.20.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\UserProfile;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * UserProfile module main class
 */
class UserProfile extends \kabar\Module\Module\Module
{
    const IN_FOOTER = true;
    const SINGLE    = true;

    /**
     * User prifile sections and fields
     * @var array
     */
    protected $sections;

    // INTERFACE

    /**
     * Setup user profile
     */
    public function __construct()
    {
        $this->fieldTemplatesDir = $this->getTemplatesDirectory().'Fields'.DIRECTORY_SEPARATOR;

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
        $storage                     = new \kabar\Utility\Storage\UserMeta;
        $storage->setPrefix($this->getUserSettingsPrefix($id));
        $template                    = new \kabar\Component\Template\Template;
        $template($this->getTemplatesDirectory().'Section.php');
        $template->title             = $title;
        $template->containerCssClass = $position == 'bottom' ? 'section-postion-bottom' : 'section-position-top';
        $this->sections[$id]         = new \kabar\Component\Form\Form(
            $id,
            '',
            '',
            $storage,
            $template,
            $this->fieldTemplatesDir
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
    public function getSetting($userId, $sectionId, $setting) {
        return get_user_meta($userId, $this->getUserSettingsPrefix($sectionId).$setting, self::SINGLE);
    }

    // INTERNAL

    /**
     * Return user meta settins prefix
     * @param  string $id
     * @return string
     */
    protected function getUserSettingsPrefix($id)
    {
        return $id.'-';
    }

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
