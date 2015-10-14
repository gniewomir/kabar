<?php
/**
 * Base class for fields
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage fields
 * @see        https://codex.wordpress.org/Function_Reference/add_meta_box
 */

namespace kabar\Utility\Fields;

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
     * Make sure that copy won't interfere with source object
     * @since  0.37.3
     * @return void
     */
    public function __clone()
    {
        $this->storage = clone $this->storage;
    }

    /**
     * Checks if field has storage object assigned
     * @since  0.31.0
     * @return bool
     */
    public function hasStorage()
    {
        return isset($this->storage);
    }

    /**
     * Bind storage object to this field
     * @param \kabar\Utility\Storage\InterfaceStorage $storage
     */
    public function setStorage(\kabar\Utility\Storage\InterfaceStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Returns field storage object
     * @since 0.37.2
     * @return \kabar\Utility\Storage\InterfaceStorage $storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Search field storage for provided value, and return id's array
     * @since  0.50.0
     * @return array
     */
    public function searchStorage($value)
    {
        return $this->storage->search($this->getSlug(), $value);
    }

    /**
     * Get field value for particular id
     * @since  0.50.0
     * @param  integer $id
     * @return mixed
     */
    public function getForId($id)
    {
        if (!$this->hasStorage()) {
            trigger_error('Field don\'t have storage object.', E_USER_ERROR);
        }
        $backup        = $this->storage;
        $this->storage = clone $backup;
        $this->storage->setId($id);
        $value         = $this->get();
        $this->storage = $backup;
        return $value;
    }

    /**
     * Get field value for particular id
     * @since  0.50.0
     * @param  integer $id
     * @param  mixed   $value
     */
    public function saveForId($id, $value)
    {
        if (!$this->hasStorage()) {
            trigger_error('Field don\'t have storage object.', E_USER_ERROR);
        }
        $backup        = $this->storage;
        $this->storage = clone $backup;
        $this->storage->setId($id);
        $this->storage->updated($this->getSlug(), $value);
        $this->save();
        $this->storage = $backup;
    }

    /**
     * Returns field value
     */
    abstract public function get();

    /**
     * Saves field value
     */
    abstract public function save();
}
