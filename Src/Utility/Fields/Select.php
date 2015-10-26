<?php
/**
 * Select field class
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage fields
 */

namespace kabar\Utility\Fields;

/**
 * Handles redering select field, keeping text field save/get methods
 */
class Select extends Text
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
     * Additional description of field
     * @var string
     */
    protected $help;

    /**
     * Setup field
     *
     * Passing null as default value will add empty option to select field which will be selected by default
     *
     * @param string $slug
     * @param string $title
     * @param array  $options Options to populate select field in label => value pairs
     * @param mixed  $default
     * @param string $help
     */
    public function __construct($slug, $title, $options, $default = null, $help = '')
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->options  = $options;
        $this->default  = $default;
        $this->help     = $help;
    }

    /**
     * Render field
     * @return \kabar\Utility\Template\Template
     */
    public function render()
    {
        $template = parent::render();

        $template->options  = $this->options;
        $template->default  = $this->default;

        return $template;
    }
}
