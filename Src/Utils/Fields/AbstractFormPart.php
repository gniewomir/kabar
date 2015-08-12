<?php
/**
 * Base class for form parts that are not typical fields
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 * @see        https://codex.wordpress.org/Function_Reference/add_meta_box
 */

namespace kabar\Utils\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Form part abstraction and utility functions
 */
abstract class AbstractFormPart implements InterfaceFormPart
{
    const TEMPLATES_DIRECTORY = 'Templates';
    const ASSETS_DIRECTORY    = 'assets';

    /**
     * Render field
     * @return /kabar/Component/Template/Template
     */
    abstract public function render();

    /**
     * Returns field slug
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Returns field css class
     * @return string
     */
    public function getCssClass()
    {
        $class          = explode('\\', get_class($this));
        $fieldCssClass  = implode('-', $class);
        array_pop($class);
        $fieldsCssClass = implode('-', $class);

        return $fieldsCssClass.' '.$fieldCssClass;
    }

    /**
     * Returns path to templates directory
     * @return string
     */
    public function getTemplatesDir()
    {
        return __DIR__.DIRECTORY_SEPARATOR.self::TEMPLATES_DIRECTORY.DIRECTORY_SEPARATOR;
    }

    /**
     * Returns uri of fields assets directory
     * @return string
     */
    protected function getAssetsUri()
    {
        if (empty($this->assetsUri)) {
            $this->assetsUri = plugins_url('', __FILE__).'/'.self::ASSETS_DIRECTORY.'/';
        }
        return $this->assetsUri;
    }

    /**
     * Returns slug of whole library
     * @return string
     */
    public function getLibrarySlug()
    {
        return ServiceLocator::VENDOR_NAMESPACE;
    }

    /**
     * Returns version of library
     * @return string
     */
    public function getLibraryVersion()
    {
        return ServiceLocator::VERSION;
    }
}
