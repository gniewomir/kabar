<?php
/**
 * Storage (strategy) interface
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage FormFieldsStorage
 */

namespace kabar\Utility\Storage;

/**
 * Storage interface
 */
interface InterfaceStorage
{
    /**
     * Sets prefix
     * @param  string $prefix
     * @return void
     */
    public function setPrefix($prefix);

    /**
     * Set ID just in case storage object cannot determine it automaticaly
     * @since 0.27.7
     * @param integer $id
     */
    public function setId($id);

    /**
     * Returns prefixed field key
     * @param  string $key
     * @return string
     */
    public function getPrefixedKey($key);

    /**
     * Returns storage key
     * @since  0.50.0
     * @param  string $key
     * @return string
     */
    public function getStorageKey($key);

    /**
     * Returns updated value and allows setting value for particular key
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    public function updated($key, $value = null);

    /**
     * Saves value in storage
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    public function store($key, $value);

    /**
     * Retrieve value from storage
     * @param  string $key
     * @return mixed
     */
    public function retrieve($key);

    /**
     * Search for key/value pair and return array of ids
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public function search($key, $value);
}
