<?php

namespace kabar;

/**
 * Class autoloader
 *
 * Provides autoloading functionality for kabar library API
 *
 * @author  Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since   1.0.0
 * @package kabar
 */
final class Autoloader
{
    const CLASS_EXT                 = '.php';
    const VENDOR_NAMESPACE          = 'kabar';

    /**
     * Registered namespaces
     * @since 2.16.0
     * @var array
     */
    private $namespaces = array();

    /**
     * Already known class paths
     * @since 2.16.0
     * @var array
     */
    private $classPaths = array();

    /**
     * Class names
     * @since 2.16.0
     * @var array
     */
    private $classNames = array();

    /**
     * Register autoloader
     */
    public function __construct()
    {
        if (!spl_autoload_register(array($this, 'load'), true)) {
            trigger_error('kabar library autoloader failed to register.', E_USER_ERROR);
        }
        $this->namespaces[self::VENDOR_NAMESPACE] = __DIR__;
    }

    /**
     * Register namespace and it's path, as a extension for the default set of modules and components
     * @since  2.16.0
     * @param  string $namespace
     * @param  string $path
     * @return void
     */
    public function register($namespace, $path)
    {
        if (isset($this->namespaces[$namespace])) {
            trigger_error('You cannot set multiple path\'s for namespace "'.$namespace.'".', E_USER_ERROR);
        }
        $this->namespaces[$namespace] = $path;
    }

    /**
     * Load class
     * @param string $class Class name
     * @return bool
     */
    public function load($class)
    {
        // require, as we already know path to this class
        if (isset($this->classPaths[$class])) {
            require $this->classPaths[$class];
            return true;
        }

        // find class path
        $path = $this->getClassPath($class);

        // require class
        if (!empty($path)) {
            require $path;
            return true;
        }

        // not 'our' class, pass it to next autoloader
        return false;
    }

    /**
     * Returns module class name by his name
     * @since  2.16.0
     * @param  string $name
     * @param  string $type
     * @return string
     */
    public function getClassName($name, $type)
    {
        $id = $type.'+'.$name;
        // check if we already found this class
        if (isset($this->classNames[$id])) {
            return $this->classNames[$id];
        }
        // look for class in registered namespace paths in reverse order
        // return class name, from last registered namespace
        // this allows to override default modules
        $namespaces = array_reverse($this->namespaces);
        foreach ($namespaces as $namespace => $path) {
            $dir = rtrim($path, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$type.DIRECTORY_SEPARATOR.$name;
            if (is_dir($dir)) {
                $this->classNames[$id] = '\\'.$namespace.'\\'.$type.'\\'.$name.'\\'.$name;
                break;
            }
        }
        if (empty($this->classNames[$id])) {
            trigger_error('Can\'t locate '.$type.' "'.$name.'"', E_USER_ERROR);
        }
        return $this->classNames[$id];
    }

    /**
     * Returns module directory
     * @since  2.16.0
     * @param  string $name
     * @param  string $type
     * @return string
     */
    public function getModuleDirectory($name, $type)
    {
        $class = $this->getClassName($name, $type);
        if (isset($this->classPaths[$class])) {
            return dirname($this->classPaths[$class]);
        } else {
            return dirname($this->getClassPath($class));
        }
    }

    /**
     * Get class path
     * @since  2.16.0
     * @param  string $class
     * @return string
     */
    private function getClassPath($class)
    {
        // normalize class names
        $class = ltrim($class, '\\');

        // check if it is one of 'our' classes, bail otherwise
        $parts  = explode('\\', $class);
        $vendor = array_shift($parts);
        if (!isset($this->namespaces[$vendor])) {
            return false;
        }

        // find class path and store it for later
        array_unshift($parts, rtrim($this->namespaces[$vendor], DIRECTORY_SEPARATOR));
        $this->classPaths[$class] = implode(DIRECTORY_SEPARATOR, $parts).self::CLASS_EXT;

        return $this->classPaths[$class];
    }
}
