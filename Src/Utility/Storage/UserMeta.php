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
final class UserMeta implements InterfaceStorage
{
    /**
     * @see https://codex.wordpress.org/Function_Reference/get_user_meta
     */
    const SINGLE = true;

    /**
     * Prefix for keys
     * @var string
     */
    private $prefix;

    // INTERFACE

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
     * Returns storage id
     * @since  2.25.1
     * @param  string $key
     * @return string
     */
    public function getStorageId($key)
    {
        return $this->getFieldId($key);
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
        update_user_meta($this->getUserId(), $this->getFieldId($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
    */
    public function retrieve($key)
    {
        return get_user_meta($this->getUserId(), $this->getFieldId($key), self::SINGLE);
    }

    // INTERNAL

    /**
     * Get user id
     * @since  2.25.1
     * @return integer
     */
    private function getUserId() {
        if (!defined('IS_PROFILE_PAGE')) {
            trigger_error('Not a profile page.', E_USER_ERROR);
        }
        global $user_id;
        if (empty($user_id)) {
            trigger_error('Cannot determine user ID.', E_USER_ERROR);
        }
        return (integer) $user_id;
    }
}
