<?php
/**
 * Base class for fields
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 * @see        https://codex.wordpress.org/Function_Reference/add_meta_box
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Field abstraction and utility functions
 */
abstract class AbstractField extends AbstractFormPart implements InterfaceField
{
    /**
     * Field data storage
     * @var \kabar\Utility\Storage\InterfaceStorage
     */
    protected $storage;

    /**
     * Returns field value
     */
    abstract public function get();

    /**
     * Saves field value
     */
    abstract public function save();

    /**
     * Checks if field has storage object assigned
     * @since  2.31.0
     * @return bool
     */
    public function hasStorage()
    {
        return isset($this->storage);
    }

    /**
     * Binds storage object to this field
     * @param \kabar\Utility\Storage\InterfaceStorage $storage
     */
    public function setStorage(\kabar\Utility\Storage\InterfaceStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Returns field storage object
     * @since 2.37.2
     * @return \kabar\Utility\Storage\InterfaceStorage $storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Make sure that copy won't interfere with source object
     * @since  2.37.3
     * @return void
     */
    public function __clone()
    {
        $this->storage = clone $this->storage;
    }
}
