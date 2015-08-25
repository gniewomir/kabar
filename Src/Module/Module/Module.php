<?php
/**
 * Module without any functionality, providing utility functions for other classes
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\Module;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Class providing base for module you can extend
 */
class Module
{
    const TEMPLATES_DIRECTORY = 'Templates';
    const ASSETS_DIRECTORY    = 'assets';

    /**
     * Current module name
     * @since 2.14.0
     * @var string
     */
    protected $moduleName;

    /**
     * Current module type
     * @since 2.14.0
     * @var string
     */
    protected $moduleType;

    /**
     * Current module slug used as ID
     * @since 2.14.0
     * @var string
     */
    protected $moduleSlug;

    /**
     * Templates directory
     * @since 2.14.0
     * @var string
     */
    protected $templatesDirectory;

    /**
     * Assets uri
     * @since 2.14.0
     * @var string
     */
    protected $assetsUri;

    /**
     * Returns module name
     * @return string
     */
    protected function getModuleName()
    {
        if (empty($this->moduleName)) {
            $class            = explode('\\', $this->getModuleClass());
            $this->moduleName = end($class);
        }
        return $this->moduleName;
    }

    /**
     * Returns current module class
     * @return string
     */
    protected function getModuleClass()
    {
        return get_class($this);
    }

    /**
     * Returns current module slug used as ID
     * @since 2.15.0
     * @return string
     */
    protected function getModuleSlug()
    {
        if (empty($this->moduleSlug)) {
            $this->moduleSlug = str_replace('\\', '-', $this->getModuleClass());
            $this->moduleSlug = trim($this->moduleSlug, '-');
        }
        return $this->moduleSlug;
    }

    /**
     * Return module type 'Modules', 'Widgets', 'Component' etc.
     * @return string
     */
    protected function getModuleType()
    {
        if (empty($this->moduleType)) {
            $this->moduleType = explode('\\', $this->getModuleClass());
            array_shift($this->moduleType);
            $this->moduleType = array_shift($this->moduleType);
        }
        return $this->moduleType;
    }

    /**
     * Returns slug for main library
     * @return string
     */
    protected function getLibrarySlug()
    {
        return ServiceLocator::VENDOR_NAMESPACE;
    }

    /**
     * Returns version of library
     * @return string
     */
    protected function getLibraryVersion()
    {
        return ServiceLocator::VERSION;
    }

    /**
     * Returns uri of module assets directory
     * @return string
     */
    protected function getAssetsUri()
    {
        if (empty($this->assetsUri)) {
            // check if module is in plugin or in theme directory
            // and return appropriate url
            if (strpos($this->getTemplatesDirectory(), get_stylesheet_directory()) !== false) {
                $assets = str_replace(
                    self::TEMPLATES_DIRECTORY,
                    self::ASSETS_DIRECTORY,
                    $this->getTemplatesDirectory()
                );
                $assets = str_replace(
                    get_stylesheet_directory(),
                    '',
                    $assets
                );
                $this->assetsUri = get_stylesheet_directory_uri().$assets;
            } else {
                $this->assetsUri = plugins_url('', dirname(__DIR__)).'/'.$this->getModuleType().'/'.$this->getModuleName().'/'.self::ASSETS_DIRECTORY.'/';
            }
        }
        return $this->assetsUri;
    }

    /**
     * Returns default templates directory
     * @return string
     */
    protected function getTemplatesDirectory()
    {
        if (empty($this->templatesDirectory)) {
            $this->templatesDirectory = ServiceLocator::getModuleDirectory(
                $this->getModuleName(),
                $this->getModuleType()
            ).DIRECTORY_SEPARATOR.self::TEMPLATES_DIRECTORY.DIRECTORY_SEPARATOR;
        }
        return $this->templatesDirectory;
    }
}
