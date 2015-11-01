<?php
/**
 * Kabar Library
 *
 * @package    kabar
 * @subpackage kabar
 * @since      0.50.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar;

/**
* Kabar library Dependancy Ijection Container & Autoloader
*/
final class Kabar extends \Dice\Dice
{

    /**
     * Registered namespaces
     * @var array
     */
    private $namespaces = array();

    /**
     * Namespaces for shared instances
     * @var array
     */
    private $shared = array(
        'Module',
        'Service',
        'Factory'
    );

    /**
     * Setup library
     */
    public function __construct()
    {
        $this->register('kabar', __DIR__.DS);
        $this->register('examples', dirname(__DIR__).'Doc'.DS.'Examples'.DS);
        spl_autoload_register(
            array($this, 'autoload'),
            true, // throw exception on error
            true  // prepend autoload queue
        );
    }

    /**
     * Register namespace and its path with class autoloader
     * @param  string $namespace
     * @param  string $path
     * @return void
     */
    public function register($namespace, $path)
    {
        $this->namespaces[$namespace] = $path;
    }

    /**
     * Return registered namespaces
     * @deprecated since 0.50.0, introduced only to allow ServiceLocator backwards compatibility, will be removed in future relase
     * @return array
     */
    public function getRegistered()
    {
        return $this->namespaces;
    }

    /**
     * Create object instance according to rules
     * @param  string $name
     * @param  array  $args
     * @param  array  $share
     * @return object
     */
    public function create($name, array $args = array(), array $share = array())
    {
        if (is_string($name)) {
            $name = $this->parseName($name);
        }

        return parent::create($name, $args, $share);
    }

    /**
     * Autoloader
     * @param  string $class
     * @return void
     */
    public function autoload($class)
    {
        foreach ($this->namespaces as $prefix => $baseDir) {
            // does the class use the namespace prefix?
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                // no, move to the next registered namespace
                continue;
            }

            // get the relative class name
            $relativeClass = substr($class, $len);

            // check if class instance should be shared
            $this->maybeShare($class);

            // replace the namespace prefix with the base directory, replace namespace
            // separators with directory separators in the relative class name, append
            // with .php
            $file = $baseDir.str_replace('\\', '/', $relativeClass).'.php';

            // if the file exists, require it
            if (file_exists($file)) {
                require $file;
                // class found, nothing more to do
                break;
            }
        }
    }

    /**
     * Make class instance shared if it matches our convention
     *
     * - class name is equal to preceding namespace name
     * - namespace preceding class namespace is on the shared list
     *
     * @deprecated public visibility of this method is deprecated since 0.50.0, it will be made private in future relase
     * @param  string $class
     * @return bool
     */
    public function maybeShare($class)
    {
        $class = $this->parseName($class);
        $parts = explode('\\', $class);
        if (array_pop($parts) === array_pop($parts) && in_array(array_pop($parts), $this->shared)) {
            $this->addRule($class, array('shared' => true));

            return true;
        }

        return false;
    }

    /**
     * Normalize class name
     * @param  string $name
     * @return string
     */
    public function parseName($name)
    {
        // '/' instead '\\'
        if (strpos($name, '/') !== false) {
            $name = trim($name, '/');
            // shortcut, 'kabar/Module/Scripts' instead 'kabar/Module/Scripts/Scripts'
            if (substr_count($name, '/') === 2) {
                $name   = explode('/', $name);
                $name[] = end($name);
                $name   = implode('/', $name);
            }
            $name = str_replace('/', '\\', $name);
        }
        $name = ltrim($name, '\\');
        return $name;
    }
}
