<?php
/**
 * Module without any functionality, providing utility functions for other classes
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.17.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\Sidebars;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Sidebars module main class
 */
class Sidebars extends \kabar\Module\Module\Module
{

    /**
     * Sidebars and widgets data
     * @var array
     */
    protected $sidebars;

    /**
     * Active Sidebars and widgets data
     * @var array
     */
    protected $activeSidebars;

    /**
     * Setup
     */
    public function __construct(
        \kabar\Module\Config\Config $config,
        \kabar\Module\Cache\Cache $cache
    ) {
        $this->requireBeforeAction('after_setup_theme');

        $this->config = $config;
        $this->cache  = $cache;

        // unregister default widgets
        if ($this->config->widgetizedpages->disableDefaultWidgets) {
            add_action('widgets_init', array($this, 'unregisterDefaultWidgets'), 11);
        }
        // Add widgetized pages sidebars and widgets
        add_action('widgets_init', array($this, 'registerSidebars'), 9);
    }

    // MODULE API

    /**
     * Register sidebar with module
     * @param  array $arguments
     * @return void
     */
    public function register($arguments)
    {
        $id = $arguments['id'];
        $this->sidebars[$id] = $arguments;
    }

    /**
     * Output sidebar
     */
    public function render($id)
    {
        echo $this->cache->cacheHtml(
            $id,
            $this->getModuleName(),
            array($this, 'getSidebar'),
            array($id)
        );
    }

    /**
     * Purge sidebar from cache
     * @param  string $id
     * @return void
     */
    public function purgeCachedSidebar($id)
    {
        $this->cache->delete($id, $this->getModuleName());
    }

    /**
     * Purge all cached sidebars from cache
     * @return void
     */
    public function purgeAllCachedSidebars()
    {
        foreach ($this->sidebars as $id => $sidebarArguments) {
            $this->purgeCachedSidebar($id);
        }
    }

    // INTERNAL

    /**
     * WordPress action. Registers all added sidebars with WordPress.
     * @return void
     */
    public function registerSidebars()
    {
        // will be empty when doing wp cron job
        if (empty($this->sidebars)) {
            return;
        }
        foreach ($this->sidebars as $id => $arguments) {
            register_sidebar($arguments);
        }
        // if in admin - initialize cache cleaner
        if (is_admin()) {
            $this->cacheCleaner = new Cleaner($this);
        }
    }

    /**
     * Get widget area
     * @return void
     */
    public function getSidebar($id)
    {
        ob_start();
        if (!dynamic_sidebar($id)) {
            $template = ServiceLocator::getNew('Component', 'Template');
            $template($this->getTemplatesDirectory().'/NoWidgets.php');
            $template->librarySlug = $this->getLibrarySlug();
            echo $template;
        }
        return ob_get_clean();
    }

    /**
     * WordPress action. Unregister default widgets
     * @return void
     */
    public function unregisterDefaultWidgets()
    {
        unregister_widget('WP_Widget_Pages');
        unregister_widget('WP_Widget_Calendar');
        unregister_widget('WP_Widget_Archives');
        unregister_widget('WP_Widget_Links');
        unregister_widget('WP_Widget_Meta');
        unregister_widget('WP_Widget_Search');
        unregister_widget('WP_Widget_Text');
        unregister_widget('WP_Widget_Categories');
        unregister_widget('WP_Widget_Recent_Posts');
        unregister_widget('WP_Widget_Recent_Comments');
        unregister_widget('WP_Widget_RSS');
        unregister_widget('WP_Widget_Tag_Cloud');
        unregister_widget('WP_Nav_Menu_Widget');
    }
}
