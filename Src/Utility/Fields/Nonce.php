<?php
/**
 * Handles rendering and validation of Nonce field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 * @see        https://codex.wordpress.org/WordPress_Nonces
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Nonce field class
 */
class Nonce implements InterfaceFormPart
{

    const REFFERER_FIELD = true;
    const ECHO_FIELD     = false;

    /**
     * Field slug
     * @var string
     */
    protected $slug;

    /**
     * Action protected by nonce
     * @var string
     */
    protected $action;

    /**
     * Setup nonce field
     * @param string $slug
     * @param string $action
     */
    public function __construct($slug, $action)
    {
        $this->slug   = $slug;
        $this->action = $action;
    }

    /**
     * Output nonce field in form
     * @see    https://codex.wordpress.org/Function_Reference/wp_nonce_field
     * @return /kabar/Component/Template/Template
     */
    public function render()
    {
        $template = ServiceLocator::getNew('Component', 'Template');
        $template($this->getTemplatesDir().DIRECTORY_SEPARATOR.'Nonce.php');
        $template->nonce = wp_nonce_field($this->action, $this->getSlug(), self::REFFERER_FIELD, self::ECHO_FIELD);
        return $template;
    }

    /**
     * Get nonce field value, tru if nonce validates, false otherwise
     * @return bool
     * @see    https://codex.wordpress.org/Function_Reference/wp_verify_nonce
     */
    public function get()
    {
        if (!isset($_POST[$this->getSlug()])) {
            return false;
        }
        if (!wp_verify_nonce($_POST[$this->getSlug()], $this->action)) {
            return false;
        }
        return true;
    }

    /**
     * Returns field slug
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Returns path to templates directory
     * @return string
     */
    public function getTemplatesDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR;
    }
}
