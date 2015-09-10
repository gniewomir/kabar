<?php
/**
 * HTTPPost storage
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage FormFieldsStorage
 */

namespace kabar\Utility\Storage;

/**
 * Class for storig data in post meta
 */
final class HTTPPost implements InterfaceStorage
{

    /**
     * Prefix for keys
     * @var string
     */
    private $prefix = '';

    // INTERFACE

    /**
     * Setup storage
     * @since 2.15.0
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
        return $this->getFieldId();
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
        $_POST[$this->getFieldId($key)] = $value;
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
}
