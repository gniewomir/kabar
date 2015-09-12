<?php
/**
 * Site options storage
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.30.0
 * @package    kabar
 * @subpackage FormFieldsStorage
 */

namespace kabar\Utility\Storage;

/**
 * Class for storing data in site options
 */
final class SiteOptions implements InterfaceStorage
{
    const AUTOLOAD = true;

    /**
     * Id
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
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Set ID just in case storage object cannot determine it automaticaly
     *
     * In case of site options does nothing.
     *
     * @param integer $id
     */
    public function setId($id)
    {
        trigger_error('This storage method don\'t support id\'s', E_USER_WARNING);
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
        update_option($this->getPrefixedKey($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
    */
    public function retrieve($key)
    {
        return get_option($this->getPrefixedKey($key), null);
    }

    /**
     * Search for key/value pair and return array of id's
     * @param  string  $key
     * @param  mixed   $value
     * @return integer
     */
    public function search($key, $value)
    {
        trigger_error('This storage method don\'t support id\'s', E_USER_WARNING);
        return 0;
    }
}
