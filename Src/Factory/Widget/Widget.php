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
     * Cache module
     * @var \kabar\Modue\Cache\Cache
     */
    private $cache;

    /**
     * Setup widgets module
     * @param \kabar\Factory\Template\Template $templateFactory
     */
    public function __construct(
        \kabar\Factory\Template\Template $templateFactory,
        \kabar\Module\Cache\Cache $cache
    ) {
        $this->templateFactory = $templateFactory;
        $this->cache           = $cache;
    }

    /**
     * Register widgets
     * @param  \kabar\Widget... $widget
     * @return void
     */
    public function register(\kabar\Widget $widget)
    {
        $widgets = func_get_args();
        foreach ($widgets as $widget) {
            $this->registerSingleWidget($widget);
        }
    }

    /**
     * Registers single widget
     * @param  \kabar\Widget $widget
     * @return void
     */
    private function registerSingleWidget($widget)
    {
        $factory   = $this->getWordPressWidgetFactory();
        $key       = $this->getWidgetKey($widget);
        $decorator = new Decorator(
            $widget,
            $this->templateFactory->create(),
            $this->cache
        );
        $factory->widgets[$key] = $decorator;
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

    /**
     * Returns WordPress widgets factory instance
     * @return \WP_Widget_Factory
     */
    private function getWordPressWidgetFactory()
    {
        return $GLOBALS['wp_widget_factory'];
    }

    // /**
    //  * Return registered widgets array
    //  * @return array<\WP_Widget>
    //  */
    // private function getWidgets()
    // {
    //     return getWidgetFactory()->widgets;
    // }

    // /**
    //  * Use widget stucture outside sidebar
    //  * @param  string $id   CSS id of widget
    //  * @return void
    //  */
    // public function render($id, $options = array())
    // {
    //     $id               = trim($id, '#');
    //     $widget           = $this->templateFactory->create();

    //     $widget($this->config['template']);
    //     $widget->widgetId = $id;
    //     foreach ($options as $name => $value) {
    //         $widget->$name = $value;
    //     }
    //     $widget = $this->wrapForReuse(
    //         $id,
    //         $this->config['css_classes'],
    //         $this->render($widget)
    //     );

    //     echo $widget;
    // }

    // INTERNAL

    // /**
    //  * Wraps provided string in standard widget wrapper for widgetized page
    //  *
    //  * Function created, to allow echoing widget outside sidebar
    //  *
    //  * @param  string $id
    //  * @param  string $class
    //  * @param  string $content
    //  * @return string
    //  */
    // private function wrapForReuse($id, $class, $content)
    // {
    //     return implode('', array(
    //         sprintf(self::DEFAULT_WRAPER_BEFORE, $id, $class),
    //         $content,
    //         self::DEFAULT_WRAPER_AFTER
    //     ));
    // }
}
