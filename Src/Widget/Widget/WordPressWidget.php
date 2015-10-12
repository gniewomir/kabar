<?php
/**
 * WordPress widget class child, that is ancestor for all kabar widgets providing them with acces to FieldsCollection API.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage widgets
 */

namespace kabar\Widget\Widget;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Decorator for default WordPress widget class connecting it with our Widget module
 */
class WordPressWidget extends \WP_Widget
{

    const BASE_CLASS_NAME = 'WordPressWidget';

    /**
     * Configuration pulled from parent module
     * @var array
     */
    protected $config = array();

    /**
     * Fields collection
     * @var \kabar\Widget\Widget\FieldsCollection
     */
    protected $fieldsCollection;

    /**
     * Widget template
     * @var \kabar\Utility\Template\Template
     */
    protected $template;

    /**
     * Register widget with WordPress.
     */
    public function __construct()
    {
        $this->config = $this->getParentModule()->config();
        parent::__construct(
            $this->config['id'], // Base ID
            $this->config['title'], // Name
            array(
                'description' => $this->config['description'],
                'classname'   => $this->config['css_classes'],
            )
        );
        $this->fieldsCollection = $this->getParentModule()->fields(new FieldsCollection($this));
        add_action('template_redirect', array($this, 'setupTemplates'));
    }

    /**
     * Create template objects for each Widget
     * @return void
     */
    public function setupTemplates()
    {
        $sidebars = wp_get_sidebars_widgets();
        foreach ($sidebars as $sidebar => $widgets) {
            foreach ($widgets as $index => $widgetId) {
                if (strpos($widgetId, $this->config['id']) === 0) {
                    $template                   = ServiceLocator::get('Factory', 'Template')->create();
                    $template($this->config['template']);
                    $template                   = $this->getParentModule()->objects($widgetId, $template);
                    $this->templates[$widgetId] = $template;
                }
            }
        }
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        $widgetId = $args['widget_id'];
        $template = $this->templates[$widgetId];
        $template = $this->fieldsCollection->populateTemplate(
            $args,
            $instance,
            $template
        );
        $template = $this->getParentModule()->render($template);
        echo $args['before_widget'].$template.$args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        $this->fieldsCollection->render($instance);
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $newInstance Values just sent to be saved.
     * @param array $oldInstance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($newInstance, $oldInstance)
    {
        return $this->fieldsCollection->updateFields($newInstance, $oldInstance);
    }

    /**
     * Returns parent module
     * @return object
     */
    protected function getParentModule()
    {
        $module = get_class($this);
        $module = str_replace(self::BASE_CLASS_NAME, '', $module);
        return ServiceLocator::get('Widget', $module);
    }
}
