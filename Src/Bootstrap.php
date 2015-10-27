<?php
/**
 * Kabar Library bootstrap
 *
 * @package    kabar
 * @subpackage kabar
 * @since      0.50.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

define('KABAR_NAMESPACE', 'kabar');
define('KABAR_VERSION', '0.50.0');

// setup dependancies autoloading
require dirname(__DIR__) . '/vendor/autoload.php';

// setup kabar autoloading/DIC
require __DIR__ . '/Kabar.php';
$kabar = new kabar\Kabar();

// setup library locator
require __DIR__ . '/Global.php';
Kabar::setup($kabar);

/**
 * setup service locator
 *
 * @deprecated since 0.38.0, provided only for backwards compatibility
 */
require __DIR__ . DIRECTORY_SEPARATOR . 'ServiceLocator.php';
\kabar\ServiceLocator::setup($kabar);
