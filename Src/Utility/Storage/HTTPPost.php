<?php
/**
 * HTTPPost storage
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage FormFieldsStorage
 */

namespace kabar\Utility\Storage;

/**
 * Class for storing data in $_POST array
 */
class HTTPPost implements InterfaceStorage
{
    /**
     * Id
     * @var integer
     */
    protected $id;

    /**
     * Prefix for keys
     * @var string
     */
    protected $prefix;

    /**
     * Updated field values
     * @since 0.50.0
     * @var array
     */
    protected $updated = array();

    // INTERFACE

    /**
     * Setup storage object
     * @since 0.31.0
     * @param string       $prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Set ID just in case storage object cannot determine it automaticaly
     *
     * Does nothing in HTTPPost storage
     *
     * @since 0.27.7
     * @param integer $id
     */
    public function setId($id)
    {
        trigger_error('This storage method don\'t support id\'s', E_USER_ERROR);
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
    public function getPrefixedKey($key)
    {
        return $this->prefix.$key;
    }

    /**
     * Returns storage key
     * @since  0.50.0
     * @param  string $key
     * @return string
     */
    public function getStorageKey($key)
    {
        return $this->getPrefixedKey($key);
    }

    /**
     * Returns updated value
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    public function updated($key, $value = null)
    {
        if (!is_null($value)) {
            $this->updated[$key] = $value;
        }

        if (isset($this->updated[$key])) {
            return $this->updated[$key];
        }

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
        $_POST[$this->getPrefixedKey($key)] = $value;
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
    */
    public function retrieve($key)
    {
        return $this->updated($key);
    }

    /**
     * Search for key/value pair and return array of id's
     *
     * Stub. POST data are not associated with any id's,
     * so we are returning empty array every time
     *
     * @since  0.27.7
     * @param  string  $key
     * @param  mixed   $value
     */
    public function search($key, $value)
    {
        trigger_error('This storage method don\'t support id\'s', E_USER_ERROR);
    }
}
