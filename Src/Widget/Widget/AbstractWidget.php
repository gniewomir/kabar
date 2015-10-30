<?php
/**
 * Wrapper module for our widgets.
 *
 * @see        \kabar\Widget\Widget\WordPressWidget
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage Widgets
 */

namespace kabar\Widget\Widget;

/**
 * Provides ability to create WordPress widgets on the fly.
 */
abstract class AbstractWidget extends \kabar\Module
{

    const DEFAULT_WRAPER_BEFORE    = '<section id="%1$s" class="widget %2$s">';
    const DEFAULT_WRAPER_AFTER     = '</section>';

    /**
     * Template factory
     * @var \kabar\Factory\Template\Teamplate
     */
    private $templateFactory;

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
     * Setup module
     * @param \kabar\Factory\Template\Template|null $templateFactory
     */
    public function __construct(\kabar\Factory\Template\Template $templateFactory = null)
    {
        add_action('widgets_init', array($this, 'register'));
        $this->config = $this->config();

        $this->templateFactory = $templateFactory;

        /**
         * @deprecated since 0.50.0
         */
        if (is_null($this->templateFactory)) {
            $this->templateFactory = \kabar\ServiceLocator::get('Factory', 'Template');
        }
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
    private function getWidgetClass()
    {
        // determine new widget class name
        $className = $this->getModuleName().'WordPressWidget';

        // base widget that we extending
        $baseClass = '\kabar\Widget\Widget\WordPressWidget';

        // do not try to autoload
        if (!class_exists($className, false)) {
            // create WordPress widget class for this widget module
            eval('class '.$className.' extends '.$baseClass.' { private $kabarModule = '.$this->getModuleClass().'; };');
        }

        // return created class name
        return $className;
    }

    /**
     * Inject dependancies to template created for WordPress widget instance ( which is not an object, just array of data )
     *
     * @since  0.0.0
     * @param  string                              $widgetId Uniqe id for this wiget
     * @param  \kabar\Utility\Template\Template $template
     * @return \kabar\Utility\Template\Template
     */
    public function objects($widgetId, \kabar\Utility\Template\Template $template)
    {
        $template->widgetId = $widgetId;
        return $template;
    }

    /**
     * Widget configuration fields, shared between all WordPress widget instances ( which aren't objects, just arrays of data )
     *
     * IMPORTANT: Fields ID's have to be valid php variable names, as later they are extracted in template
     *
     * @param \kabar\Widget\Widget\FieldsCollection $fieldsCollection
     * @return FieldsCollection
     */
    public function fields(\kabar\Widget\Widget\FieldsCollection $fieldsCollection)
    {
        return $fieldsCollection;
    }

    /**
     * Render widget using prepopulated (with widget fields and objects) template for current WordPress widget instance
     *
     * @param  \kabar\Utility\Template\Template $template Prepopulated template object
     * @return \kabar\Utility\Template\Template
     */
    public function render(\kabar\Utility\Template\Template $template)
    {
        return $template;
    }

    /**
     * Use widget stucture outside sidebar
     * @param  string $id   CSS id of widget
     * @return void
     */
    public function reuse($id, $options = array())
    {
        $config           = $this->config();
        $id               = trim($id, '#');
        $widget           = $this->templateFactory->create();
        $widget($config['template']);
        $widget->widgetId = $id;
        foreach ($options as $name => $value) {
            $widget->$name = $value;
        }
        $widget = $this->wrapForReuse(
            $id,
            $config['css_classes'],
            $this->render($widget)
        );
        echo $widget;
    }

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
