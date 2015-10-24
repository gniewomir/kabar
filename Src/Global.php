<?php
/**
 * Global access to Kabar library
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
     * Iject library to locator
     * @param  \kabar\Kabar $kabar
     * @return void
     */
    public static function setup(\kabar\Kabar $kabar)
    {
        if (self::$library) {
            trigger_error('You cannot switch library instance at runtime.', E_USER_ERROR);
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
}
