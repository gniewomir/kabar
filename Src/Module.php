<?php
/**
 * Common foundation for all Kabar modules
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar;

/**
 * Base for all Kabar modules provides common utility methods accros whole library
 */
abstract class Module
{
    /**
     * Current module class
     * @since 0.50.0
     * @var string
     */
    private $moduleClass;

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
    private $moduleTemplatesDirectory;

    /**
     * Assets uri
     * @since 0.14.0
     * @var string
     */
    private $moduleAssetsUri;

    /**
     * Returns current module class
     * @return string
     */
    protected function getModuleClass()
    {
        if (!$this->moduleClass) {
            $this->moduleClass = get_class($this);
        }
        return $this->moduleClass;
    }

    /**
     * Returns module name
     * @return string
     */
    public function getModuleName()
    {
        if (!$this->moduleName) {
            /**
             * Fastest way
             * @see http://stackoverflow.com/questions/19901850/how-do-i-get-an-objects-unqualified-short-class-name
             */
            $this->moduleName = substr(strrchr($this->getModuleClass(), '\\'), 1);
        }
        return $this->moduleName;
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
        if (!$this->moduleType) {
            $exploded         = explode('\\', $this->getModuleClass());
            $this->moduleType = $exploded[1];
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
        if (!$this->moduleCssClass) {
            $module = explode('-', $this->getModuleSlug());
            $module = array_slice($module, 0, -1);
            $module = implode('-', $module);
            $class  = $this->getModuleSlug();

            $this->moduleCssClass = $module . ' ' . $class;
        }
        return $this->moduleCssClass;
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
        if (empty($this->moduleAssetsUri)) {
            // check if module is in plugin or in theme directory
            // and return appropriate url
            if (strpos($this->getTemplatesDirectory(), get_stylesheet_directory()) !== false) {
                $assetsDirectory                = $this->getModuleDirectory() . 'assets';
                $assetsDirectoryRelativeToTheme = str_replace(
                    get_stylesheet_directory(),
                    '',
                    $assetsDirectory
                );
                $this->moduleAssetsUri = get_stylesheet_directory_uri() . $assetsDirectoryRelativeToTheme;
            } else {
                $this->moduleAssetsUri = plugins_url('/assets/', $this->getModuleDirectory());
            }
        }
        return $this->moduleAssetsUri;
    }

    /**
     * Returns default templates directory
     * @return string
     */
    protected function getTemplatesDirectory()
    {
        if (empty($this->moduleTemplatesDirectory)) {
            $this->moduleTemplatesDirectory = $this->getModuleDirectory() . 'Templates' . DIRECTORY_SEPARATOR;
        }
        return $this->moduleTemplatesDirectory;
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
                $this->moduleDirectory = dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->getModuleType() . DIRECTORY_SEPARATOR . $this->getModuleName() . DIRECTORY_SEPARATOR;
            } else {
                $reflection            = new \ReflectionClass($this->getModuleClass());
                $this->moduleDirectory = dirname($reflection->getFileName()) . DIRECTORY_SEPARATOR;
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
            throw new \Exception('Class "' . $this->getModuleClass() . '" requires to ' . $what . ' to not be empty at this point.', 1);
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
            throw new \Exception('Class "' . $this->getModuleClass() . '" requires to be instantiated before "' . $action . '" action.', 1);
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
            throw new \Exception('Class "' . $this->getModuleClass() . '" requires to be instantiated after "' . $action . '" action.', 1);
        }
    }
}
