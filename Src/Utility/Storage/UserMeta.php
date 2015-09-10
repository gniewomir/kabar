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
     * Id
     * @since 2.25.7
     * @var integer
     */
    private $id;

    /**
     * Prefix for keys
     * @var string
     */
    private $prefix;

    // INTERFACE

    /**
     * Set ID just in case storage object cannot determine it automaticaly
     * @since 2.25.7
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = (integer) $id;
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
     * Returns prefixed field key
     * @return string
     */
    public function getPrefixedKey($key)
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
        return isset($_POST[$this->getPrefixedKey($key)]) ? $_POST[$this->getPrefixedKey($key)] : null;
    }

    /**
     * Save value to storage
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function store($key, $value)
    {
        update_user_meta($this->getUserId(), $this->getPrefixedKey($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
    */
    public function retrieve($key)
    {
        return get_user_meta($this->getUserId(), $this->getPrefixedKey($key), self::SINGLE);
    }

    // INTERNAL

    /**
     * Get user id
     * @since  2.25.1
     * @return integer
     */
    private function getUserId()
    {
        if ($this->id) {
            return $this->id;
        }
        global $user_id;
        if (empty($user_id) || !defined('IS_PROFILE_PAGE')) {
            trigger_error('Cannot determine user ID.', E_USER_ERROR);
        }
        return (integer) $user_id;
    }
}
