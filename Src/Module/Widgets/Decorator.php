<?php
/**
 * WordPress widget class child, that is ancestor for all kabar widgets providing them with acces to FieldsCollection API.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.50.0
 * @package    kabar
 * @subpackage widgets
 */

namespace kabar\Module\Widgets;

/**
 * Decorator for default WordPress widget class connecting it with our Widget module
 */
class Decorator extends \WP_Widget
{

    /**
     * Configuration pulled from parent module
     * @var array
     */
    private $config = array();

    /**
     * Fields collection
     * @var \kabar\Widget\Widget\FieldsCollection
     */
    private $fieldsCollection;

    /**
     * Widget template
     * @var \kabar\Utility\Template\Template
     */
    private $templateFactory;

    private $widget;


    /**
     * Register widget with WordPress.
     */
    public function __construct(\kabar\Factory\Template\Template $templateFactory, \kabar\Widget $widget)
    {
        $this->widget = $widget;
        $this->config = $this->widget->config();
        parent::__construct(
            $this->config['id'], // Base ID
            $this->config['title'], // Name
            array(
                'description' => $this->config['description'],
                'classname'   => $this->config['css_classes'],
            )
        );
        // $this->fieldsCollection = $this->widget->fields(new FieldsCollection($this));
    }

    /**
     * Return widget object
     * @return \kabar\Widget
     */
    public function getWidget()
    {
        return $this->widget;
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
        $template = $this->templateFactory->create();
        $template($this->config['template']);
        $template = $this->fieldsCollection->populateTemplate(
            $args,
            $instance,
            $template
        );
        $template = $this->widget->render($template);
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
}
