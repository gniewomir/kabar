<?php
/**
 * Wrapper module for our widgets.
 *
 * @see        \kabar\Widget\Widget\WordPressWidget
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage Widgets
 */

namespace kabar\Widget\Widget;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Provides ability to create WordPress widgets on the fly.
 */
abstract class AbstractWidget extends \kabar\Module\Module\Module
{
    /**
     * Configuration array
     * @var array
     */
    protected $config;

    /**
     * Returns widget config
     * @return array
     */
    abstract public function config();

    /**
     * Require and hook registration of widget
     */
    public function __construct()
    {
        add_action('widgets_init', array($this, 'register'));
        $this->config = $this->config();
    }

    /**
     * Register widget
     */
    public function register()
    {
        register_widget($this->getWidgetClass());
    }

    /**
     * Returns our widget class name, and acctualy creates appropriate class, if it doesn't exists already.
     * @return string
     */
    protected function getWidgetClass()
    {
        // determine new widget class name
        $className = $this->getModuleName().'WordPressWidget';

        // base widget that we extending
        $baseClass = '\kabar\Widget\Widget\WordPressWidget';

        // do not try to autoload
        if (!class_exists($className, false)) {
            // create WordPress widget class for this widget module
            eval('class '.$className.' extends '.$baseClass.' {};');
        }

        // return created class name
        return $className;
    }

    /**
     * Inject dependancies to template created for WordPress widget instance ( which is not an object, just array of data )
     *
     * @since  2.0.0
     * @param  string                              $widgetId Uniqie id fo this wiget
     * @param  \kabar\Component\Template\Template $template
     * @return \kabar\Component\Template\Template
     */
    public function objects($widgetId, \kabar\Component\Template\Template $template)
    {
        return $template;
    }

    /**
     * Widget configuration fields, shared between all WordPress widget instances ( which aren't objects, just arrays of data )
     *
     * IMPORTANT: Fields ID's have to be valid php variable names, as later they are extracted in template
     *
     * @param \kabar\Widget\Widget\FieldsCollection $fieldsCollection
     * @return array
     */
    public function fields(\kabar\Widget\Widget\FieldsCollection $fieldsCollection)
    {
        return $fieldsCollection;
    }

    /**
     * Render widget using prepopulated (with widget fields and objects) template for current WordPress widget instance
     *
     * @param  \kabar\Component\Template\Template $template Prepopulated template object
     * @return \kabar\Component\Template\Template
     */
    public function render(\kabar\Component\Template\Template $template)
    {
        return $template;
    }

    /**
     * Use widget stucture outside sidebar
     * @param  string $id   CSS id of widget
     * @param  string $term Category slug
     * @return void
     */
    public function reuse($id, $options = array())
    {
        $config           = $this->config();
        $id               = trim($id, '#');
        $widget           = ServiceLocator::getNew('Component', 'Template');
        $widget($config['template']);
        $widget->widgetId = $id;
        foreach ($options as $name => $value) {
            $widget->$name = $value;
        }
        $widget = ServiceLocator::get('Module', 'Pages')
            ->wrapForReuse(
                $id,
                $config['css_classes'],
                $this->render($widget)
            );
        echo $widget;
    }
}
