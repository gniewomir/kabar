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
     * @param string $prefix
     * @return void
     */
    public function setPrefix($prefix);

    /**
     * Returns field Id for provided key
     * @param string $key
     * @return string
     */
    public function getFieldId($key);

    /**
     * Returns updated value
     * @param string $key
     * @return mixed
     */
    public function updated($key);

    /**
     * Saves value in storage
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function store($key, $value);

    /**
     * Retrieve value from storage
     * @param  string $key
     * @return void
     */
    public function retrieve($key);
}
