<?php
/**
 * Widgets module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.50.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\Widgets;

/**
 * Widgets module main class
 */
final class Widgets extends \kabar\Module
{

    /**
     * Template factory
     * @var \kabar\Factory\Template
     */
    private $templateFactory;

    /**
     * Setup widgets module
     * @param \kabar\Factory\Template\Template $templateFactory
     */
    public function __construct(\kabar\Factory\Template\Template $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }

    /**
     * Register widgets
     * @param  \kabar\Widget... $widget
     * @return void
     */
    public function register(\kabar\Widget $widget)
    {
        $widgets = func_get_args();
        $factory = $this->getWidgetFactory();
        foreach ($widgets as $widget) {
            $key     = $this->getWidgetKey($widget);
            $adapter = new Decorator($this->templateFactory, $widget);
            $factory->widgets[$key] = $adapter;
        }
    }

    /**
     * Get widget registration key
     *
     * WARNING: changes to this method may break backwards compatibility
     *
     * @param  string $widget
     * @return string
     */
    public function getWidgetKey($widget)
    {
        return $widget->getModuleName().'WordPressWidget';
    }

    private function getWidgetFactory()
    {
        return $GLOBALS['wp_widget_factory'];
    }

    private function getWidgets()
    {
        return getWidgetFactory()->widgets;
    }
}
