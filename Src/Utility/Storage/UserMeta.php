<?php
/**
 * User meta storage
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.24.4
 * @package    kabar
 * @subpackage FormFieldsStorage
 */

namespace kabar\Utility\Storage;

/**
 * Class for storig data in user meta
 */
class UserMeta implements InterfaceStorage
{
    /**
     * @see https://codex.wordpress.org/Function_Reference/get_user_meta
     */
    const SINGLE = true;

    /**
     * Prefix for keys
     * @var string
     */
    protected $prefix;

    /**
     * Setup storage
     * @param string $prefix Prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Sets prefix used for storing values
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Returns prefixed key
     * @return string
     */
    public function getFieldId($key)
    {
        return $this->prefix.$key;
    }

    /**
     * Returns updated value
     * @param  string $key
     * @return mixed
     */
    public function updated($key)
    {
        return isset($_POST[$this->getFieldId($key)]) ? $_POST[$this->getFieldId($key)] : null;
    }

    /**
     * Save value to storage
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function store($key, $value)
    {
        $userId = get_current_user_id();
        if (empty($userId)) {
            trigger_error('Cannot determine user ID. You cannot store user meta before "init" action.', E_USER_WARNING);
            return;
        }
        update_user_meta($userId, $this->getFieldId($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
     * @see    https://codex.wordpress.org/Function_Reference/get_post_meta
    */
    public function retrieve($key)
    {
        $userId = get_current_user_id();
        if (empty($userId)) {
            trigger_error('Cannot determine user ID. You cannot retrieve user meta before "init" action.', E_USER_WARNING);
            return;
        }
        return get_user_meta($userId, $this->getFieldId($key), self::SINGLE);
    }
}
