<?php
/**
 * Simple templating engine
 *
 * Provides very basic templating using encapsulation and php.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Component
 */
namespace kabar\Component\Template;

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
