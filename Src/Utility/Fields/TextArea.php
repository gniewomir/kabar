<?php
/**
 * Textarea field class
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage fields
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Textarea field class
 */
class TextArea extends Text
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
     * Setup field
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
}
