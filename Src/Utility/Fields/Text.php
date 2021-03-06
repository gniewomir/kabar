<?php
/**
 * Text field class
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Text field class
 */
class Text extends AbstractField
{

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
     * @var string
     */
    protected $default;

    /**
     * Additional description of field
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
    public function __construct($slug, $title, $default = '', $help = '')
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->default  = $default;
        $this->help     = $help;
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
     * @return \kabar\Component\Template\Template
     */
    public function render()
    {
        $template           = $this->getTemplate();
        $template->id       = $this->storage->getPrefixedKey($this->getSlug());
        $template->cssClass = $this->getCssClass();
        $template->title    = $this->title;
        $template->help     = $this->help;
        $value              = $this->get();
        $template->value    = empty($value) ? $this->default : $value;
        return $template;
    }

    /**
     * Save new field value
     * @return string
     */
    public function save()
    {
        // Sanitize user input.
        $value = sanitize_text_field($this->storage->updated($this->getSlug()));

        // store value
        $this->storage->store($this->getSlug(), $value);

        return $value;
    }
}
