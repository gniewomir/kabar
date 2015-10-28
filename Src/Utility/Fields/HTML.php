<?php
/**
 * HTML Textarea field class
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.26.4
 * @package    kabar
 * @subpackage fields
 */

namespace kabar\Utility\Fields;

/**
 * Textarea field class
 */
class HTML extends TextArea
{

    const IN_FOOTER = true;

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
     * Field template file path
     * @var string
     */
    protected $template;

    /**
     * Additional description of field
     * @var string
     */
    protected $help;

    /**
     * Enable WYSIWYG editor?
     * @var bool
     */
    protected $wysiwyg;

    /**
     * Setup field
     * @param string $slug
     * @param string $title
     * @param string $default
     * @param string $help
     * @param bool   $wysiwyg
     */
    public function __construct($slug, $title, $default = '', $help = '', $wysiwyg = true)
    {
        $this->slug    = $slug;
        $this->title   = $title;
        $this->default = $default;
        $this->help    = $help;
        $this->wysiwyg = $wysiwyg;
    }

    /**
     * Render field
     * @return \kabar\Utility\Template\Template
     */
    public function render()
    {
        $template = parent::render();

        $template->wysiwyg = $this->wysiwyg;

        return $template;
    }

    /**
     * Save new field value
     * @return string
     */
    public function save()
    {
        // Sanitize user input.
        $value = wp_kses_post($this->storage->updated($this->getSlug()));

        // store value
        $this->storage->store($this->getSlug(), $value);

        return $value;
    }
}
