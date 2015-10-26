<?php
/**
 * Global access for Kabar library
 *
 * @package    kabar
 * @subpackage kabar
 * @since      0.50.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

class Kabar
{
    /**
     * Kabar library instance
     * @var \kabar\Kabar
     */
    private static $library;

    /**
     * Private constructor, this class is not meant to be instantiated
     */
    private function __construct()
    {

    }

    /**
     * Iject library to locator
     * @param  \kabar\Kabar $kabar
     * @return void
     */
    public static function setup(\kabar\Kabar $kabar)
    {
        if (self::$library) {
            throw new \Exception('You cannot switch library instance at runtime.', 1);
        }
        self::$library = $kabar;
    }

    /**
     * Return library instance
     * @return \kabar\Kabar
     */
    public static function library()
    {
        return self::$library;
    }

    /**
     * Render template tag
     * @param  strign $tagName
     * @param  array  $arguments
     * @return mixed
     */
    public static function tag($tagName, $arguments)
    {
        return self::$library->create('kabar\\Module\\Tag\\Tag')->render($tagName, $arguments);
    }
}
