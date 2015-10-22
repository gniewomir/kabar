<?php
/**
 * Admin notices component
 *
 * @package    kabar
 * @subpackage component
 * @since      0.19.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\AdminUI\Notice;

class Notice extends \kabar\Module\Module\Module
{
    const NOTICES_EXPIRATION = MINUTE_IN_SECONDS;
    const NAG_CSS_CLASS      = 'update-nag';
    const SUCCESS_CSS_CLASS  = 'updated';
    const ERROR_CSS_CLASS    = 'error';

    /**
     * Current user id
     * @var int
     */
    private $userId;

    /**
     * Admin notices
     * @var array
     */
    private $notices = array();

    // INTERFACE

    /**
     * Setup module
     */
    public function __construct()
    {
        if (!is_admin()) {
            return;
        }

        $this->requireBeforeAction('admin_notices');
        add_action('admin_notices', array($this, 'render'));
    }

    /**
     * Add success message
     * @param string $message
     * @return void
     */
    public function sucess($message)
    {
        $this->add(self::SUCCESS_CSS_CLASS, $message);
    }

    /**
     * Add error message
     * @param string $message
     * @return void
     */
    public function error($message)
    {
        $this->add(self::ERROR_CSS_CLASS, $message);
    }

    /**
     * Add update nag
     * @param string $message
     * @return void
     */
    public function update($message)
    {
        $this->add(self::NAG_CSS_CLASS, $message);
    }

    /**
     * Add admin notice
     * @param string $class
     * @param string $message
     * @return void
     */
    public function add($class, $message)
    {
        $this->notices[] = array(
            'class'   => $class,
            'message' => $message
        );
        // store notices, in case we will be redirected
        set_transient(
            $this->getTransientId(),
            serialize($this->notices),
            self::NOTICES_EXPIRATION
        );
    }

    // INTERNAL

    /**
     * Returns notices transient ID
     * @return string
     */
    private function getTransientId()
    {
        $this->requireAfterAction('set_current_user');
        $this->requireNotEmpty('User ID', get_current_user_id());
        return $this->getLibrarySlug().'_admin_notices_user_'.$this->userId;
    }

    /**
     * WordPress action. Show admin notices.
     * @internal
     * @return void
     */
    public function render()
    {
        // notices not set, which probably means that we where redirected
        // get them from transient
        if (empty($this->notices)) {
            $this->notices = unserialize(get_transient($this->getTransientId()));
        }
        delete_transient($this->getTransientId());
        if (empty($this->notices)) {
            return;
        }
        foreach ($this->notices as $notice) {
            echo '<div class="'.$notice['class'].'"><p>'.$notice['message'].'</p></div>';
        }
    }
}
