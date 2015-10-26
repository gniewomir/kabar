<?php
/**
 * Module without any functionality, providing utility functions for other classes
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar;

/**
 * Class providing base for module you can extend
 */
class Module
{
    const TEMPLATES_DIRECTORY = 'Templates';
    const ASSETS_DIRECTORY    = 'assets';

    /**
     * Current module name
     * @since 0.14.0
     * @var string
     */
    private $moduleName;

    /**
     * Current module type
     * @since 0.14.0
     * @var string
     */
    private $moduleType;

    /**
     * Module directory
     * @since 0.50.0
     * @var string
     */
    private $moduleDirectory;

    /**
     * Current module slug used as ID
     * @since 0.14.0
     * @var string
     */
    private $moduleSlug;

    /**
     * Templates directory
     * @since 0.14.0
     * @var string
     */
    private $templatesDirectory;

    /**
     * Assets uri
     * @since 0.14.0
     * @var string
     */
    private $assetsUri;

    /**
     * Returns module name
     * @return string
     */
    public function getModuleName()
    {
        if (empty($this->moduleName)) {
            /**
             * Fastest way
             * @see http://stackoverflow.com/questions/19901850/how-do-i-get-an-objects-unqualified-short-class-name
             */
            $this->moduleName = substr(strrchr($this->getModuleClass(), '\\'), 1);
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
     * @since 0.15.0
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
     * @since  0.24.4
     * @since  0.50.0 is protected
     * @return string
     */
    protected function getCssClass()
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
        return KABAR_NAMESPACE;
    }

    /**
     * Returns version of library
     * @return string
     */
    protected function getLibraryVersion()
    {
        return KABAR_VERSION;
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
                $assets = $this->getModuleDirectory().self::ASSETS_DIRECTORY;
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
            $this->templatesDirectory = $this->getModuleDirectory().DIRECTORY_SEPARATOR.self::TEMPLATES_DIRECTORY.DIRECTORY_SEPARATOR;
        }
        return $this->templatesDirectory;
    }

    /**
     * Get module directory
     * @todo   Find more efficent way of getting module path - without reflection or accessing global state. No, we cannot assume that every child will be instantiated by library
     * @since  0.50.0
     * @return string
     */
    protected function getModuleDirectory()
    {
        if (!$this->moduleDirectory) {
            if (strpos($this->getModuleClass(), KABAR_NAMESPACE) === 0) {
                $this->moduleDirectory = dirname(__FILE__).DIRECTORY_SEPARATOR.$this->getModuleType().DIRECTORY_SEPARATOR.$this->getModuleName().DIRECTORY_SEPARATOR;
            } else {
                $reflection            = new \ReflectionClass($this->getModuleClass());
                $this->moduleDirectory = dirname($reflection->getFileName()).DIRECTORY_SEPARATOR;
            }
        }
        return $this->moduleDirectory;
    }

    /**
     * Check if required value is already set and not empty
     * @since  0.19.0
     * @return void
     */
    protected function requireNotEmpty($what, $value)
    {
        if (empty($value)) {
            throw new \Exception('Class "'.$this->getModuleClass().'" requires to '.$what.' to not be empty at this point.', 1);
        }
    }

    /**
     * Check if we are before action required by module
     * @since  0.19.0
     * @return void
     */
    protected function requireBeforeAction($action)
    {
        if (defined('KABAR_UNIT_TESTING') && KABAR_UNIT_TESTING === true) {
            return;
        }
        if (did_action($action)) {
            throw new \Exception('Class "'.$this->getModuleClass().'" requires to be instantiated before "'.$action.'" action.', 1);
        }
    }

    /**
     * Check if we are after action, that needs to be executed before module
     * @since  0.19.0
     * @return void
     */
    protected function requireAfterAction($action)
    {
        if (defined('KABAR_UNIT_TESTING') && KABAR_UNIT_TESTING === true) {
            return;
        }
        if (!did_action($action)) {
            throw new \Exception('Class "'.$this->getModuleClass().'" requires to be instantiated after "'.$action.'" action.', 1);
        }
    }
}
