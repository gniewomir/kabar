<?php
/**
 * Internal field. It't won't be rendered or saved when submiting form.
 *
 * Main goal of this class is keeping relevant data - that should not be
 * available on front end - in the same context.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.29.1
 * @package    kabar
 * @subpackage fields
 */

namespace kabar\Utility\Fields;

/**
 * Internal field class
 */
class Internal extends AbstractField
{

    /**
     * Field slug
     * @var string
     */
    protected $slug;

    /**
     * Setup text field
     * @param string $slug
     */
    public function __construct($slug)
    {
        $this->slug     = $slug;
    }

    /**
     * Get field value
     * @return string
     */
    public function get()
    {
        return $this->storage->retrieve($this->getSlug());
    }

    /**
     * Render field
     * @return \kabar\Utility\Template\Template
     */
    public function render()
    {
        return '';
    }

    /**
     * Save new field value
     * @return string
     */
    public function save()
    {
        $value = $this->storage->updated($this->getSlug());
        $this->storage->store($this->getSlug(), $value);
        return $value;
    }
}
