<?php
/**
 * WordPress widget interface
 *
 * @since      0.50.0
 * @package    kabar
 * @subpackage interfaces
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Interfaces;

interface WPWidget
{
    /**
     * Takes widget args and instance
     * @param  array $args
     * @param  array $instance
     * @return void
     */
    public function widget($args, $instance);

    /**
     * Updates widget instance with new values
     * @param  array $newInstance New values
     * @param  array $oldInstance Old values
     * @return array              Updated values
     */
    public function update($newInstance, $oldInstance);

    /**
     * Takes widget instance
     * @param  array $instance
     * @return void
     */
    public function form($instance);
}
