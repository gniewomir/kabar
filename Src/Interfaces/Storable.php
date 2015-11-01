<?php
/**
 * Storable interface
 *
 * @since      0.50.0
 * @package    kabar
 * @subpackage interfaces
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Interfaces;

interface Storable
{
    /**
     * Retrieve stored key
     * @param  string $key
     * @param  mixed  $value
     * @return mixed
     */
    public function store($key, $value);
}
