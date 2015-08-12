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

namespace kabar\Utils\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Field abstraction and utility functions
 */
abstract class AbstractField extends AbstractFormPart implements InterfaceField
{
    /**
     * Field slug
     * @var string
     */
    protected $slug;

    /**
     * Field data storage
     * @var \kabar\Utils\Storage\InterfaceStorage
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
     * Binds storage object to this field
     * @param \kabar\Utils\Storage\InterfaceStorage $storage
     */
    public function setStorage(\kabar\Utils\Storage\InterfaceStorage $storage)
    {
        $this->storage = $storage;
    }
}
