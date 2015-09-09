<?php

namespace kabar\Utility\Fields;

/**
 * Interface for fields
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */
interface InterfaceField
{
    /**
     * Render field
     * @return \kabar\Component\Template\Template
     */
    public function render();

    /**
     * Returns field value
     */
    public function get();

    /**
     * Saves field value
     */
    public function save();

    /**
     * Get field slug
     * @return string
     */
    public function getSlug();

    /**
     * Binds storage object to this field
     * @param \kabar\Utility\Storage\InterfaceStorage $storage
     * @return void
     */
    public function setStorage(\kabar\Utility\Storage\InterfaceStorage $storage);
}
