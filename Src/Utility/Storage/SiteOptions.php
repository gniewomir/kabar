<?php
/**
 * Site options storage
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.30.0
 * @package    kabar
 * @subpackage FormFieldsStorage
 */

namespace kabar\Utility\Storage;

/**
 * Class for storing data in site options
 */
final class SiteOptions extends HTTPPost implements InterfaceStorage
{
    // INTERFACE

    /**
     * Save value to storage
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function store($key, $value)
    {
        update_option($this->getStorageKey($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
    */
    public function retrieve($key)
    {
        return get_option($this->getStorageKey($key), null);
    }
}
