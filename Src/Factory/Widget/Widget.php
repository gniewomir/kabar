<?php
/**
 * Widgets module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.50.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Factory\Widget;

/**
 * Widgets module main class
 */
final class Widget extends \kabar\Module
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
        $factory = $this->getWordPressWidgetFactory();
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
        return $widget->getModuleName() . 'WordPressWidget';
    }

    private function getWordPressWidgetFactory()
    {
        return $GLOBALS['wp_widget_factory'];
    }

    private function getWidgets()
    {
        return getWidgetFactory()->widgets;
    }



    /**
     * Use widget stucture outside sidebar
     * @param  string $id   CSS id of widget
     * @return void
     */
    public function reuse($id, $options = array())
    {
        $id               = trim($id, '#');
        $widget           = $this->templateFactory->create();

        $widget($this->config['template']);
        $widget->widgetId = $id;
        foreach ($options as $name => $value) {
            $widget->$name = $value;
        }

        $widget = $this->wrapForReuse(
            $id,
            $this->config['css_classes'],
            $this->render($widget)
        );

        echo $widget;
    }

    // INTERNAL

    /**
     * Wraps provided string in standard widget wrapper for widgetized page
     *
     * Function created, to allow echoing widget outside sidebar
     *
     * @param  string $id
     * @param  string $class
     * @param  string $content
     * @return string
     */
    private function wrapForReuse($id, $class, $content)
    {
        return implode('', array(
            sprintf(self::DEFAULT_WRAPER_BEFORE, $id, $class),
            $content,
            self::DEFAULT_WRAPER_AFTER
        ));
    }

}
