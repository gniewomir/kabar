<?php
/**
 * WordPress widget decorator, allowing for communication with kabar widget objects
 *
 * @since      0.50.0
 * @package    kabar
 * @subpackage widgets
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Factory\Widget;

use \kabar\Interfaces\WPWidget;
use \kabar\Interfaces\Cacheable;

/**
 * Decorator for default WordPress widget class connecting it with our Widget module
 */
class Decorator extends \WP_Widget implements Cacheable, WPWidget
{
    /**
     * SplSubject interface implementation, to fulfill Cacheable interface requirements
     */
    use \kabar\Traits\SplSubject;

    /**
     * Cache module
     * @var \kabar\Module\Cache\Cache
     */
    private $cache;

    /**
     * Form
     * @var \kabar\Utility\Form\Form
     */
    private $form;

    /**
     * Kabar Widget
     * @var \kabar\Widget
     */
    private $widget;

    /**
     * Template
     * @var \kabar\Utility\Template\Template
     */
    private $template;

    /**
     * Configuration pulled from parent module
     * @var array
     */
    private $config = array();

    /**
     * Cache ID, set when required data is avaiable, before comunicating with cache object
     * @var string
     */
    private $cacheId;

    // INTERFACE

    /**
     * Register widget with WordPress.
     */
    public function __construct(
        \kabar\Widget                    $widget,
        \kabar\Utility\Template\Template $template,
        \kabar\Module\Cache\Cache        $cache
    ) {
        $this->widget   = $widget;
        $this->config   = $this->widget->config();

        $template($this->config['template']);
        $this->template = $template;

        $this->cache    = $cache;
        $this->attach($this->cache);

        $this->storage  = new Storage($this);

        parent::__construct(
            $this->config['id'],
            $this->config['title'],
            array(
                'description' => $this->config['description'],
                'classname'   => $this->config['css_classes'],
            )
        );
    }

    /**
     * Display widget
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance)
    {
        // echo cached and bail
        if ($this->cache->isCached($this)) {
            echo $this->cache->getCached($this);

            return;
        }

        // set cache id, it will be reffering to current widget instance
        // so widget can by cached and reliably retrieved later
        $this->setCacheId($this->id, $instance);

        $template = $this->render($args, $instance);
        $this->setCache($this, $template);
        echo $template;
    }

    /**
     * Back-end widget form.
     * @param array $instance Previously saved values from database.
     */
    public function form($instance)
    {
        $this->storage->form($instance);
        echo $this->getForm()->render();
    }

    /**
     * Sanitize widget form values as they are saved.
     * @param  array $newInstance Values just sent to be saved.
     * @param  array $oldInstance Previously saved values from database.
     * @return array              Updated safe values to be saved.
     */
    public function update($newInstance, $oldInstance)
    {
        // set cache id and notify observers about widget update
        // cache id will be reffering to stale widget cache
        // so it can be purged
        $this->setCacheId($this->id, $oldInstance);
        $this->notify();

        // update storage
        $this->storage->update($newInstance, $oldInstance);
        // and pass data trough form and fields, to perform validation
        return $this->getForm()->save();
    }

    /**
     * Cacheable interface. Return cache id
     * @return string
     */
    public function getCacheId()
    {
        return $this->cacheId;
    }

    /**
     * Cacheable interface. Check if this widget should be cached or not
     * @return boolean
     */
    public function shouldCache()
    {
        return isset($this->config['cache']) && $this->config['cache'] === true;
    }

    // INTERNALS

    /**
     * Return widget form in a lazy way
     * @return \kabar\Utility\Form\Form
     */
    private function getForm()
    {
        if (!$this->form) {
            $form = new \kabar\Utility\Form\Form(
                $this->id,
                'POST', // method
                '', // default = self
                $this->storage,
                $this->templateFactory->create()
            );
            $this->form = $this->widget->form($form);
        }
        return $this->form;
    }

    /**
     * Render widget
     * @param  array  $args     Widget arguments.
     * @param  array  $instance Saved values from database.
     * @return string
     */
    private function render($args, $instance)
    {
        $this->storage->widget($args, $instance);
        $template = $this->getForm()->getPopulatedTemplate();
        $template = $this->widget->render($template);
        return $args['before_widget'].$template.$args['after_widget'];
    }

    /**
     * Create cache id from widget instance id and its settings
     * @param string $id
     * @param array  $instance
     */
    private function setCacheId($id, $instance)
    {
        $this->cacheId = 'widget'.md5($id.serialize($instance));
    }
}
