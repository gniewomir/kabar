<?php
/**
 * Template component
 *
 * Provides very basic templating using encapsulation and php.
 *
 * @package    kabar
 * @subpackage component
 * @since      0.0.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Utility\Template;

/**
 * Template class
 */
final class Template
{

    /**
     * Template vars
     * @var array
     */
    private $vars = array();

    /**
     * Template file
     * @var string
     */
    private $file = '';

    // INTERFACE

    /**
     * Set template
     * @param string $template
     */
    public function __invoke($template)
    {
        $this->file = $template;
    }

    /**
     * Get template var
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->vars[$name];
    }

    /**
     * Set template var
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        // often template will be populated automaticaly
        // and template property will be named after form field slug,
        // we still don't want to run expensive regex to validate property name,
        // but we should check at least for hyphens - it will be common mistake
        if (strpos($name, '-') !== false) {
            throw new \InvalidArgumentException('Template property "'.$name.'" should be valid PHP variable name!', 1);
        }
        $this->vars[$name] = $value;
    }

    /**
     * Invoked when template is treated as string
     * @return string
     */
    public function __toString()
    {
        extract($this->vars);

        ob_start();
        include $this->file;

        return (string) ob_get_clean();
    }
}
