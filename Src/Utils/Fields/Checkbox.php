<?php
/**
 * Select field class
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */

namespace kabar\Utils\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Handles redering, saving and retrieveing checkbox field
 */
class Checkbox extends AbstractField
{

    const ENABLED  = 'on';
    const DISABLED = 'off';

    /**
     * Field slug
     * @var string
     */
    protected $slug;

    /**
     * Field title
     * @var stiring
     */
    protected $title;

    /**
     * Field default value
     * @var string
     */
    protected $default;

    /**
     * Field template file path
     * @var string
     */
    protected $template;

    /**
     * Setup text field
     * @param string $slug
     * @param string $title
     * @param string $default
     */
    public function __construct($slug, $title, $default = false)
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->default  = $default;
        $this->value    = self::ENABLED;
        $this->template = $this->getTemplatesDir().'Checkbox.php';
    }

    /**
     * Render field
     * @return /kabar/Component/Template/Template
     */
    public function render()
    {
        $template = ServiceLocator::getNew('Component', 'Template');
        $template($this->template);
        $template->id       = $this->storage->getFieldId($this->getSlug());
        $template->cssClass = $this->getCssClass();
        $template->title    = $this->title;
        $template->value    = $this->value;
        $template->checked  = $this->get() ? 'checked="checked"' : '';
        return $template;
    }

    /**
     * Get field value
     * @return bool
     */
    public function get()
    {
        $val = $this->storage->retrieve($this->getSlug());
        if (!$val) {
            return $this->default;
        }

        return $val == self::ENABLED;
    }

    /**
     * Save new field value
     * @return bool
     */
    public function save()
    {
        if (is_null($this->storage->updated($this->getSlug()))) {
            $value = self::DISABLED;
        } elseif ($this->storage->updated($this->getSlug()) == self::ENABLED) {
            $value = self::ENABLED;
        } else {
            $value = $this->default ? self::ENABLED : self::DISABLED;
        }

        // store value
        $this->storage->store($this->getSlug(), $value);

        return $value == self::ENABLED;
    }
}
