<?php
/**
 * Template factory
 *
 * @package    kabar
 * @subpackage factory
 * @since      2.38.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Factory\Template;

/**
 * Template objects factory
 */
class Template
{

    /**
     * Template prototype
     * @var \kabar\Component\Template\Template
     */
    private $prototype;

    /**
     * Setup template factory
     * @param \kabar\Component\Template\Template $prototype
     */
    public function __construct(\kabar\Component\Template\Template $prototype)
    {
        $this->prototype = $prototype;
    }

    /**
     * Return new template
     * @return \kabar\Component\Template\Template
     */
    public function create()
    {
        return clone $this->prototype;
    }
}
