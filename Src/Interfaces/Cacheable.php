<?php
/**
 * Cacheable interface
 *
 * @since      0.50.0
 * @package    kabar
 * @subpackage interfaces
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Interfaces;

interface Cacheable extends \SplSubject
{

    /**
     * Returns cache id for cacheable element
     * @return string
     */
    public function getCacheId();

    /**
     * If data provided by this object should be cached?
     * @return boolean
     */
    public function shouldCache();
}
