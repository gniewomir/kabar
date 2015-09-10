<?php
/**
 * Select field class
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */

namespace kabar\Utility\Fields;

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
     * @var string
     */
    protected $title;

    /**
     * Field default value
     * @var bool
     */
    protected $default;

    /**
     * Input value
     * @var string
     */
    protected $value;

    /**
     * Help text
     * @var string
     */
    protected $help;

    /**
     * Setup text field
     * @param string $slug
     * @param string $title
     * @param string $default
     * @param string $help
     */
    public function __construct($slug, $title, $default = false, $help = '')
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->default  = $default;
        $this->help     = $help;

        $this->value    = self::ENABLED;
    }

    /**
     * Render field
     * @return \kabar\Component\Template\Template
     */
    public function render()
    {
        $template           = $this->getTemplate();
        $template->id       = $this->storage->getFieldId($this->getSlug());
        $template->cssClass = $this->getCssClass();
        $template->title    = $this->title;
        $template->value    = $this->value;
        $template->help     = $this->help;
        $template->checked  = $this->get() ? 'checked' : '';
        return $template;
    }

    /**
     * Get field value
     * @return bool
     */
    public function get()
    {
        $value = $this->storage->retrieve($this->getSlug());

        if (empty($value)) {
            $value = $this->default ? self::ENABLED : self::DISABLED;
            $this->storage->store($this->getSlug(), $value);
        }

        return $value == self::ENABLED;
    }

    /**
     * Save new field value
     * @return bool
     */
    public function save()
    {
        $updated = $this->storage->updated($this->getSlug());

        if ($updated == self::ENABLED) {
            $value = self::ENABLED;
        } else {
            $value = self::DISABLED;
        }

        // store value
        $this->storage->store($this->getSlug(), $value);
        return $value == self::ENABLED;
    }
}
