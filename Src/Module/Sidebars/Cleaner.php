<?php
/**
 * Sidebars module cache cleaning
 *
 * @package    kabar
 * @subpackage module
 * @since      0.17.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Module\Sidebars;

/**
 * Purge sidebars module cache if needed
 */
final class Cleaner extends \kabar\Module
{

    /**
     * Sidebars module
     * @var \kabar\Module\Sidebars\Sidebars
     */
    private $sidebars;

    /**
     * Widgets list
     * @var array
     */
    private $widgets;

    // INTERFACE

    /**
     * Hook to all actions and filters that should trigger sidebars cache purge
     * @todo  Hook to category/tag/taxonomy creation/removal/update
     * @param \kabar\Module\Sidebars\Sidebars $sidebars
     */
    public function __construct(\kabar\Module\Sidebars\Sidebars $sidebars)
    {
        $this->sidebars = $sidebars;

        // get widgets list
        add_filter('sidebars_widgets', array($this, 'widgetsList'));

        // widget changed - purge particular sidebar
        add_filter('widget_update_callback', array($this, 'widgetUpdate'), 10, 4);

        // save/delete post - purge all sidebars
        add_action('save_post', array($this, 'postUpdatedOrDeleted'), 11);
        add_action('delete_post', array($this, 'postUpdatedOrDeleted'), 11);

        // customization screen save
        add_action('customize_save_after', array($this, 'customizationSettingsChange'), 11);
    }

    // INTERNAL

    /**
     * WordPress filter 'sidebars_widgets'. Grab widgets list
     * @internal
     * @param  array $widgets
     * @return array
     */
    public function widgetsList($widgets)
    {
        $this->widgets = $widgets;
        return $widgets;
    }

    /**
     * WordPress filter 'widget_update_callback'. Clear cache for sidebar containing current widget
     * @internal
     * @since  0.12.0
     * @param  array  $instance
     * @param  array  $newInstance
     * @param  array  $oldInstance
     * @param  object $widget
     * @return array
     */
    public function widgetUpdate($instance, $newInstance, $oldInstance, $widget)
    {
        // purge sidebar from cache
        $sidebarId = $this->widgetSidebar($widget->id);
        $this->sidebars->purgeCachedSidebar($sidebarId);

        // passtrough
        return $newInstance;
    }

    /**
     * WordPress action for 'save_post' and 'delete_post'. Clear pages cache on post save/delete
     * @internal
     * @param  int $postId
     * @return void
     */
    public function postUpdatedOrDeleted($postId)
    {
        // if revision - bail
        if (wp_is_post_revision($postId)) {
            return;
        }

        $post = get_post($postId);

        // if not published - bail
        if ($post && $post->post_status !== 'publish') {
            return;
        }

        $this->sidebars->purgeAllCachedSidebars();
    }

    /**
     * WordPress action 'customize_save_after'. Check if we need to clear sidebar cache after customizer update
     * @internal
     * @since  0.12.0
     * @param  \WP_Customize_Manager $object
     * @return void
     */
    public function customizationSettingsChange($object)
    {
        $sidebar = array();
        foreach ($object->unsanitized_post_values() as $key => $value) {
            if (strpos($key, 'widget_' . $this->getLibrarySlug()) === 0) {
                // look for changed settings for our widgets
                // change post var name to widget id
                $widgetId = substr($key, strlen('widget_'));
                $widgetId = trim(str_replace(array('[', ']'), '-', $widgetId), '-');
                // find where updated widget acctualy is
                $sidebar[] = $this->widgetSidebar($widgetId);
            } else if (strpos($key, 'sidebars_widgets[' . $this->getLibrarySlug()) === 0) {
                // look for changed settings for our sidebars
                // change post var name to sidebar id
                $key = substr($key, strlen('sidebars_widgets['));
                $sidebar[] = str_replace(']', '', $key);
            }
        }
        // bail if no changes found
        if (empty($sidebar)) {
            return;
        }
        // if our widget or sidebar changed - clear cache for sidebar
        foreach ($sidebar as $sidebarId) {
            if (!empty($sidebarId)) {
                $this->sidebars->purgeCachedSidebar($sidebarId);
            }
        }
    }

    /**
     * Find in which sidebar this particular widget id is
     * @since  0.12.0
     * @param  string $widgetId
     * @return string
     */
    private function widgetSidebar($widgetId)
    {
        $sidebars = $this->widgets;
        foreach ($sidebars as $sidebarId => $widgets) {
            foreach ($widgets as $testedWidgetId) {
                if ($testedWidgetId == $widgetId) {
                    return $sidebarId;
                }
            }
        }
        return false;
    }
}
