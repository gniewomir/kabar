<?php
/**
 * Service locator for Kabar library
 *
 * @deprecated since 0.38.0, provided only for backwards compatibility
 * @author  Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since   0.0.0
 * @package kabar
 */

namespace kabar;

/**
 * Service locator class
 * @deprecated since 0.38.0, provided only for backwards compatibility
 *
 * Most of methods in this class assumes to much about code organisation
 * and constructor signatures which may and eventualy will lead to errors.
 * It's not a fully backwards compatibile and never meant to be. It should ease
 * the pain of moving from 0.38.0 to 0.50.0, but not eliminate it completylly.
 */
final class ServiceLocator
{
    const VERSION          = '0.50.0';
    const VENDOR_NAMESPACE = 'kabar';
    const AUTOLOAD         = true;

    /**
     * Dependany injection container for library
     * @var \kabar\Kabar
     */
    private static $container;

    /**
     * Private constructor as this class in purely static
     */
    private function __construct()
    {

    }

    /**
     * Setup kabar library service loactor
     * @return void
     */
    public static function setup(\kabar\Kabar $kabar)
    {
        self::$container = $kabar;
    }

    /**
     * Register namespace and path for module location
     * @since  0.16.0
     * @param  string $namespace
     * @param  string $path
     * @return void
     */
    public static function register($namespace, $path)
    {
        self::$container->register($namespace, $path);
    }

    /**
     * Get class by type and class name
     * @param  string $type
     * @param  string $name
     * @return string
     */
    public static function getClass($type, $name)
    {
        $namespaces = self::$container->getRegistered();
        $namespaces = array_reverse($namespaces);
        foreach ($namespaces as $space => $path) {
            $relativeClass = $type.'\\'.$name.'\\'.$name;
            $file          = $path.str_replace('\\', '/', $relativeClass).'.php';
            if (file_exists($file)) {
                return self::$container->parseName('\\'.$space.'\\'.$relativeClass);
            }
        }
        trigger_error('Cannot determine class name for type:'.$type.' and name:'.$name, E_USER_ERROR);
    }

    /**
     * Get module directory
     * @param  string $name
     * @param  string $type
     * @return string
     */
    public static function getModuleDirectory($type, $name)
    {
        $namespaces = self::$container->getRegistered();
        $namespaces = array_reverse($namespaces);
        foreach ($namespaces as $space => $path) {
            $directory = $path.$type.DIRECTORY_SEPARATOR.$name;
            if (is_dir($directory)) {
                return $directory;
            }
        }
        trigger_error('Cannot determine class name for type:'.$type.' and name:'.$name, E_USER_ERROR);
    }

    /**
     * Parse arguments to DIC rules
     * @param  array<mixed> $arguments
     * @return void
     */
    private static function parseArguments($arguments)
    {
        // rule for this class
        $rule = array();

        // substitute requirements with provided objects
        foreach ($arguments as $index => $param) {
            if (is_object($param)) {
                $class = get_class($param);
                $rule['substitutions'][$class] = $param;
                unset($arguments[$index]);
            }
        }

        if (!empty($arguments)) {
            $rule['constructParams'] = array_values($arguments);
        }

        self::$container->addRule($class, $rule);
    }

    /**
     * Creates and stores or returns already created module instance
     * @param  string $type
     * @param  string $name
     * @return object
     */
    public static function get($type, $name)
    {
        $class = self::getClass($type, $name);

        $arguments = func_get_args();
        if (count($arguments) > 2) {
            // Get arguments that should be passed to module constructor
            // We don't need module type or name
            $arguments = array_slice($arguments, 2);
            // Change arguments to DIC rules
            self::parseArguments($arguments);
        }

        // Make instance shared if needed
        self::$container->maybeShare($class);
        return self::$container->create($class);
    }

    /**
     * Creates and stores or returns already created module instance
     * @param  string $type
     * @param  string $name
     * @return object
     */
    public static function getNew($type, $name)
    {
        $class = self::getClass($type, $name);

        $arguments = func_get_args();
        if (count($arguments) > 2) {
            // Get arguments that should be passed to module constructor
            // We don't need module type or name
            $arguments = array_slice($arguments, 2);
            // Change arguments to DIC rules
            self::parseArguments($arguments);
        }

        return self::$container->create($class);
    }

    /**
     * Check if module is already created
     * @param  string $type
     * @param  string $name
     * @return boolean
     */
    public static function isLoaded($type, $name)
    {
        $class = self::getClass($type, $name);
        return self::$container->isCreated($class);
    }
}
