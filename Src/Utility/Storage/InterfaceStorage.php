<?php
/**
 * Storage (strategy) interface
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
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
     * @since 2.25.7
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
     * Returns updated value
     * @param  string $key
     * @return mixed
     */
    public function updated($key);

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
}
