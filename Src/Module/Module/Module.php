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
    private $moduleName;

    /**
     * Current module type
     * @since 2.14.0
     * @var string
     */
    private $moduleType;

    /**
     * Current module slug used as ID
     * @since 2.14.0
     * @var string
     */
    private $moduleSlug;

    /**
     * Templates directory
     * @since 2.14.0
     * @var string
     */
    private $templatesDirectory;

    /**
     * Assets uri
     * @since 2.14.0
     * @var string
     */
    private $assetsUri;

    /**
     * Returns module name
     * @return string
     */
    protected function getModuleName()
    {
        if (empty($this->moduleName)) {
            /**
             * Fastest way
             * @see http://stackoverflow.com/questions/19901850/how-do-i-get-an-objects-unqualified-short-class-name
             */
            $this->moduleName = substr(strrchr($this->getModuleClass(), "\\"), 1);
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
            $exploded = explode('\\', $this->getModuleClass());
            array_shift($exploded);
            $this->moduleType = (string) array_shift($exploded);
        }
        return $this->moduleType;
    }

    /**
     * Returns field css class
     * @since  2.24.4
     * @return string
     */
    public function getCssClass()
    {
        $class = explode('\\', get_class($this));
        array_pop($class);
        $cssClass = implode('-', $class);
        return $cssClass;
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

    /**
     * Check if required value is already set and not empty
     * @since  2.19.0
     * @return void
     */
    protected function requireNotEmpty($what, $value)
    {
        if (empty($value)) {
            trigger_error('Module "'.$this->getModuleName().'" requires to '.$what.' to not be empty at this point.', E_USER_ERROR);
        }
    }

    /**
     * Check if we are before action required by module
     * @since  2.19.0
     * @return void
     */
    protected function requireBeforeAction($action)
    {
        if (did_action($action)) {
            trigger_error('Module "'.$this->getModuleName().'" requires to be called before "'.$action.'" action.', E_USER_ERROR);
        }
    }

    /**
     * Check if we are after action, that needs to be executed before module
     * @since  2.19.0
     * @return void
     */
    protected function requireAfterAction($action)
    {
        if (!did_action($action)) {
            trigger_error('Module "'.$this->getModuleName().'" requires to be called after "'.$action.'" action.', E_USER_ERROR);
        }
    }
}
