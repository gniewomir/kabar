<?php
/**
 * Header field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage fields
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Header field class
 */
class Header extends AbstractFormPart
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
     * Field template file path
     * @var string
     */
    protected $template;

    /**
     * Setup text field
     * @param string $slug
     * @param string $title
     */
    public function __construct($slug, $title)
    {
        $this->slug     = $slug;
        $this->title    = $title;
    }

    /**
     * Render field
     * @return \kabar\Utility\Template\Template
     */
    public function render()
    {
        $template           = $this->getTemplate();
        $template->title    = $this->title;
        $template->cssClass = $this->getCssClass();
        return $template;
    }
}
