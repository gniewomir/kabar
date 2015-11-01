<?php
/**
 * Retrievable interface
 *
 * @since      0.50.0
 * @package    kabar
 * @subpackage interfaces
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Interfaces;

interface Retrievable
{
    /**
     * Retrieve stored key
     * @param  string $key
     * @return mixed
     */
    public function retrieve($key);
}
