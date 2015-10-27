<?php
/**
 * Widget
 *
 * @package    kabar
 * @subpackage kabar
 * @since      0.50.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar;

/**
 * Provides OO API for WordPress Widget
 */
abstract class Widget extends \kabar\Module
{

    const DEFAULT_WRAPER_BEFORE    = '<section id="%1$s" class="widget %2$s">';
    const DEFAULT_WRAPER_AFTER     = '</section>';

    /**
     * Widget ID
     * @var string
     */
    private $id;

    /**
     * Widget title
     * @var string
     */
    private $title;

    /**
     * Widget description
     * @var string
     */
    private $description;

    /**
     * Widget css classes
     * @var string|array<string>
     */
    private $cssClasses;

    /**
     * Template name or path
     * @var string
     */
    private $defaultTemplate;

    /**
     * Config array
     * @var array
     */
    private $config;

    // INTERFACE

    /**
     * Setup widget
     * @param string               $id
     * @param string               $title
     * @param string               $description
     * @param string|array<string> $cssClasses
     * @param string               $defaultTemplate
     */
    public function __construct($id, $title, $description, $cssClasses = '', $defaultTemplate = 'Widget.php')
    {
        $this->id         = (strpos($id, $this->getLibrarySlug()) === 0) ? $id : $this->getLibrarySlug() . '_' . $id;
        $this->title      = $title;
        $this->desciption = $description;
        $this->cssClasses = !empty($cssClasses) && is_array($cssClasses) ? implode(' ', $cssClasses) : (string) $cssClasses;
        $this->cssClasses = $this->getCssClass() . ' ' . $this->cssClasses;

        $this->defaultTemplate = strpos($defaultTemplate, DIRECTORY_SEPARATOR) === false ? $this->getTemplatesDirectory() . $defaultTemplate : $defaultTemplate;

        $this->config = array(
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->desciption,
            'css_classes' => $this->cssClasses,
            'template'    => $this->defaultTemplate,
        );
    }

    /**
     * Return widget config
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Widget configuration fields, shared between all WordPress widget instances ( which aren't objects, just arrays of data )
     *
     * IMPORTANT: Fields ID's have to be valid php variable names, as later they are extracted in template
     *
     * @param  \kabar\Utility\Form\Form $form
     * @return \kabar\Utility\Form\Form
     */
    public function fields(\kabar\Utility\Form\Form $form)
    {
        return $form;
    }

    /**
     * Render widget using prepopulated (with widget fields and objects) template for current WordPress widget instance
     *
     * @param  \kabar\Utility\Template\Template $template Prepopulated template object
     * @return \kabar\Utility\Template\Template
     */
    public function render(\kabar\Utility\Template\Template $template)
    {
        return $template;
    }
}
