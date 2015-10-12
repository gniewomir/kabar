<?php
/**
 * Submit button field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Submit button field
 */
class Submit extends AbstractFormPart
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
     * Setup submit button
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
        $template->id       = $this->getSlug();
        $template->cssClass = $this->getCssClass();
        return $template;
    }
}
