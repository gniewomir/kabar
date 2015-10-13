<?php
/**
 * User component
 *
 * @package    kabar
 * @subpackage component
 * @since      2.39.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\AdminUI\User;

/**
 * Allows for easy extension of user profile
 */
final class User extends \kabar\Module\Module\Module
{
    const IN_FOOTER = true;

    /**
     * Form id
     * @var string
     */
    private $id;

    /**
     * Settings section title
     * @var string
     */
    private $title;

    /**
     * Form
     * @var \kabar\Utility\Form\Form
     */
    private $form;

    /**
     * Form position
     * @var string
     */
    private $position;

    // INTERFACE

    /**
     * Setup user component
     * @param string $id      Form ID
     * @param string $title   Form Title
     * @param string $postion Bottom or top of the user profile
     */
    public function __construct($id, $title = '', $postion = 'bottom')
    {
        $this->id       = $id;
        $this->title    = $title;
        $this->position = $postion;

        $storage                     = new \kabar\Utility\Storage\UserMeta();
        $storage->setPrefix($this->id.'-');

        $template                    = new \kabar\Utility\Template\Template();
        $template($this->getTemplatesDirectory().'Section.php');
        $template->title             = $this->title;
        $template->containerCssClass = $this->position == 'bottom' ? 'section-postion-bottom' : 'section-position-top';

        $this->form = new \kabar\Utility\Form\Form(
            $this->id,
            '',
            '',
            $storage,
            $template,
            'Table'
        );

        add_action('admin_enqueue_scripts', array($this, 'enqueueAssets'));

        add_action('show_user_profile', array($this, 'profile'));
        add_action('edit_user_profile', array($this, 'profile'));
        add_action('personal_options_update', array($this, 'update'));
        add_action('edit_user_profile_update', array($this, 'update'));
    }

    /**
     * Get component/form id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns section form object, which allows adding and removing fields
     * @return \kabar\Utility\Form\Form
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Get user setting
     * @param  string $userId
     * @param  string $setting
     * @return string
     */
    public function getSetting($userId, $setting)
    {
        $field = clone $this->form->getField($setting);
        $field->getStorage()->setId($userId);
        return $field->get();
    }

    // INTERNAL

    /**
     * WordPress action 'admin_enqueue_scripts'. Enqueue assets
     * @internal
     */
    public function enqueueAssets()
    {
        if ($this->position != 'bottom') {
            wp_enqueue_script(
                $this->getModuleSlug(),
                $this->getAssetsUri().'js/UserProfile.js',
                array(),
                $this->getLibraryVersion(),
                self::IN_FOOTER
            );
        }
    }

    /**
     * WordPress action 'show_user_profile' and 'edit_user_profile'. Show additional user profile fields
     * @internal
     * @param  \WP_User $user
     * @return void
     */
    public function profile($user)
    {
        echo $this->form->render();
    }

    /**
     * WordPress action 'personal_options_update' and 'edit_user_profile_update'. Update user profile
     * @internal
     * @param  int $userId
     * @return void
     */
    public function update($userId)
    {
        if ($this->form->sent()) {
            $this->form->save();
        }
    }
}
