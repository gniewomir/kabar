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
 * Class for storing data in user meta
 */
final class UserMeta implements InterfaceStorage
{
    /**
     * @see https://codex.wordpress.org/Function_Reference/get_metadata
     */
    const SINGLE = true;

    /**
     * Id
     * @since 2.27.7
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
     * Setup storage object
     * @since 2.31.0
     * @param string       $prefix
     * @param integer|null $id
     */
    public function __construct($prefix = '', $id = null)
    {
        $this->prefix = $prefix;
        $this->id     = $id;
    }

    /**
     * Set ID just in case storage object cannot determine it automaticaly
     * @since 2.27.7
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
        update_metadata('user', $this->getUserId(), $this->getPrefixedKey($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
    */
    public function retrieve($key)
    {
        return get_metadata('user', $this->getUserId(), $this->getPrefixedKey($key), self::SINGLE);
    }

    /**
     * Search for key/value pair and return array of id's
     * @since  2.27.7
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public function search($key, $value)
    {
        $args = array(
            'meta_key'   => $this->getPrefixedKey($key),
            'meta_value' => $value,
            'fields'     => 'ID'
        );

        $userQuery = new \WP_User_Query($args);

        return $userQuery->get_results();
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
        if ($user_id && defined('IS_PROFILE_PAGE')) {
            return (integer) $user_id;
        }
        if (defined(WP_DEBUG) && WP_DEBUG) {
            trigger_error('Cannot determine user ID.', E_USER_WARNING);
        }
        return false;
    }
}
