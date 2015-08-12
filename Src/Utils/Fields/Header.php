<?php
/**
 * Header field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */

namespace kabar\Utils\Fields;

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
        $this->template = $this->getTemplatesDir().'Header.php';
    }

    /**
     * Render field
     * @return /kabar/Component/Template/Template
     */
    public function render()
    {
        $template = ServiceLocator::getNew('Component', 'Template');
        $template($this->template);
        $template->title    = $this->title;
        $template->cssClass = $this->getCssClass();
        return $template;
    }
}
