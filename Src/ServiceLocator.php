<?php
/**
 * Service locator for this kabar library
 *
 * @author  Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since   1.0.0
 * @package kabar
 */

namespace kabar;

/**
 * Service locator class
 */
final class ServiceLocator
{
    const VERSION          = '2.16.1';
    const VENDOR_NAMESPACE = 'kabar';

    const AUTOLOADER       = '\\kabar\\Autoloader';
    const AUTOLOAD         = true;

    /**
     * Modules collection
     * @var array
     */
    private static $modules = array();

    /**
     * Setups modules library
     * @param string $customModulesPath      Path to directory where custom theme modules are stored
     * @param string $customModulesNamespace Vendor namespace for custom modules
     */
    public static function setup()
    {
        $apiClass = __CLASS__;
        if (isset(self::$modules[$apiClass])) {
            trigger_error('kabar library already loaded.', E_USER_ERROR);
        }
        require_once __DIR__.'/Autoloader.php';
        self::$modules[self::AUTOLOADER] = new Autoloader();
        self::$modules[$apiClass]        = new $apiClass;
    }

    /**
     * Register namespace and path for module location
     * @since 2.16.0
     * @param  string $namespace
     * @param  string $type
     * @param  string $path
     * @return void
     */
    public static function register($namespace, $path)
    {
        if (!isset(self::$modules[self::AUTOLOADER])) {
            trigger_error('You have to setup library first. Autoloader not found.', E_USER_ERROR);
        }
        self::$modules[self::AUTOLOADER]->register($namespace, $path);
    }

    /**
     * Creates and stores or returns already created module instance
     * @param  string $name Module name
     * @return object
     */
    public static function get($type, $name)
    {
        $class = self::$modules[self::AUTOLOADER]->getClassName(
            $name,
            $type
        );
        // Get arguments that should be passed to module constructor
        $arguments = func_get_args();
        array_shift($arguments); // we don't need module type in here
        array_shift($arguments); // we don't need module name in here
        return self::getInstance($class, $arguments);
    }

    /**
     * Creates and stores or returns already created module instance
     * @param  string $name Module name
     * @return object
     */
    public static function getNew($type, $name)
    {
        $class = self::$modules[self::AUTOLOADER]->getClassName(
            $name,
            $type
        );
        // Get arguments that should be passed to module constructor
        $arguments = func_get_args();
        array_shift($arguments); // we don't need module type in here
        array_shift($arguments); // we don't need module name in here
        return self::newInstance($class, $arguments);
    }

    /**
     * Creates and stores or returns already created module instance
     * @param  string $name Module name
     * @return object
     */
    public static function isLoaded($type, $name)
    {
        $class = self::$modules[self::AUTOLOADER]->getClassName(
            $name,
            $type
        );
        return isset(self::$modules[$class]);
    }

    /**
     * Returns module directory
     * @since  2.16.0
     * @param  string $name
     * @param  string $type
     * @return string
     */
    public static function getModuleDirectory($name, $type)
    {
        return self::$modules[self::AUTOLOADER]->getModuleDirectory(
            $name,
            $type
        );
    }

    /**
     * Private constructor called by static setup function
     */
    private function __construct()
    {

    }

    /**
     * Creates and stores or returns already created object instance
     * @param  string $class
     * @param  array  $arguments
     * @return object
     */
    private static function getInstance($class, $arguments = array())
    {
        // Trigger error if somebody is trying to pass arguments to already instantiated class
        if (!empty($arguments) && isset(self::$modules[$class])) {
            trigger_error('You cannot pass arguments to module "'.$class.'" constructor, it was already created.');
        }

        // Return module if already instantiated
        if (isset(self::$modules[$class])) {
            return self::$modules[$class];
        }

        // Trigger error if class doesn't exist
        if (!$class || !class_exists($class, self::AUTOLOAD)) {
            trigger_error('Class "'.$class.'" not found, cannot load module "'.$name.'".', E_USER_ERROR);

            return;
        }

        // Instantiate and return object if we don't have to pass any arguments to it
        if (empty($arguments)) {
            self::$modules[$class] = new $class;

            return self::$modules[$class];
        }

        // If we do, pass them trough reflection class
        $reflection = new \ReflectionClass($class);
        self::$modules[$class] = $reflection->newInstanceArgs($arguments);

        return self::$modules[$class];
    }

    /**
     * Creates new object instance
     * @param  string $class
     * @param  array  $arguments
     * @return object
     */
    private static function newInstance($class, $arguments = array())
    {
        // Quit if module don't exist
        if (!$class || !class_exists($class, self::AUTOLOAD)) {
            trigger_error('Class "'.$class.'" not found, cannot load module "'.$name.'".', E_USER_ERROR);

            return;
        }

        // Instantiate and return module if we don't have to pass any arguments to it
        if (empty($arguments)) {
            return new $class;
        }
        // If we do, pass them trough reflection class
        $reflection = new \ReflectionClass($class);

        return $reflection->newInstanceArgs($arguments);
    }
}