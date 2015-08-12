<?php
/**
 * Radio field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */

namespace kabar\Utils\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Handles redering radio field, keeping select field save/get methods
 */
class Radio extends Select
{

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
     * Select field options
     * @var array
     */
    protected $options;

    /**
     * Field default value
     * @var mixed
     */
    protected $default;

    /**
     * Field template file path
     * @var string
     */
    protected $template;

    /**
     * Setup field
     *
     * Passing null as default value will add empty option to select field which will be selected by default
     *
     * @param string $slug
     * @param string $title
     * @param array  $options Options to populate select field in label => value pairs
     * @param mixed  $default
     */
    public function __construct($slug, $title, $options, $default = null)
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->options  = $options;
        $this->default  = $default;
        $this->template = $this->getTemplatesDir().'Radio.php';
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
        $template->options  = $this->options;
        $template->default  = $this->default;
        $value = $this->get();
        $value = empty($value) ? $this->default : $this->get();
        $template->value    = $value;
        return $template;
    }
}
