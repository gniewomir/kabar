<?php
/**
 * Module providing pages, you can assembly fully from provided widgets.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage modules
 */

namespace kabar\Module\Pages;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Allows creation of pages constructed from widgets
 */
class Pages extends \kabar\Module\Module\Module
{

    const DEFAULT_WRAPER_BEFORE    = '<section id="%1$s" class="widget %2$s">';
    const DEFAULT_WRAPER_AFTER     = '</section>';

    const VIEW_QUERY_VAR           = 'filter';
    const VIEW_QUERY_VAR_VALUE     = 'widgetized';

    /**
     * Cache module
     * @since 2.12.0
     * @var \kabar\Module\Cache\Cache
     */
    protected $cache;

    /**
     * Widgetized page template name
     * @var string
     */
    protected $template;

    /**
     * Widgetized Pages
     * @since 2.12.0
     * @var array
     */
    protected $pages;

    /**
     * Widgetized Pages count
     * @var int
     */
    protected $count;

    /**
     * Sidebars and widgets data
     * @var array
     */
    protected $sidebars;

    /**
     * Widgetized Pages Sidebars and widgets data
     * @var array
     */
    protected $widgetizedPagesSidebars;

    /**
     * Active Sidebars and widgets data
     * @var array
     */
    protected $activeSidebars;

    /**
     * If we are on widgetized page
     * @var bool
     */
    private $widgetizedpage;

    /**
     * Current page id
     * @var bool
     */
    private $pageId;

    /**
     * If this page is cached
     * @var bool
     */
    private $cached;

    /**
     * Setup actions provided by module
     * @param string $pageTemplate Widgetized page template name
     */
    public function __construct($pageTemplate = 'templates/widgetized-page.php')
    {
        $this->template = $pageTemplate;

        // unregister default widgets
        if (ServiceLocator::get('Module', 'Config')->widgetizedpages->disableDefaultWidgets) {
            add_action('widgets_init', array($this, 'unregisterDefaultWidgets'), 11);
        }

        $this->cache = ServiceLocator::get('Module', 'Cache');

        // Find widgetized pages
        $this->pages = $this->getPages();

        // Check if we have cached version
        $this->cached = $this->isCached();

        // register widgets
        $this->registerWidgets();

        // Add widgetized pages sidebars and widgets
        add_action('widgets_init', array($this, 'registerSidebars'), 9);

        // Add link to particular page customization
        add_filter('page_row_actions', array($this, 'addBuildLink'), 10, 2);

        // Add separate view for landing pages in pages section of wp-admin
        $this->count = count($this->pages);
        add_filter('pre_get_posts', array($this, 'filterForView'));
        add_filter('views_edit-page', array($this, 'addView'));

        // Hook to widget update, customizer save, and post update to clear cache
        add_filter('widget_update_callback', array($this, 'filterWidgetUpdate'), 10, 4);
        add_action('customize_save_after', array($this, 'actionCustomizeSave'), 11);
        add_action('save_post', array($this, 'actionClearCache'), 11);
        add_action('delete_post', array($this, 'actionClearCache'), 11);
    }

    /**
     * Check if this page is cached
     * @return boolean
     */
    public function isCached()
    {
        if (isset($this->cached)) {
            return $this->cached;
        }

        return $this->cache->isCacheable() &&
            $this->cache->isCached(
                $this->getCacheId($this->pageId),
                $this->getModuleName()
            );
    }

    /**
     * Get widgetized pages
     * @since 2.12.19
     * @return array
     */
    public function getPages()
    {
        if (!$this->cache->isCached('list', 'Widgetized')) {
            $arguments = array(
                'post_type'      => 'page',
                'post_status'    => 'publish',
                'fields'         => 'ids',
                'posts_per_page' => -1,
                'meta_query'  => array(
                    array(
                        'key'     => '_wp_page_template',
                        'value'   => $this->template,
                        'compare' => '=',
                    ),
                )
            );
            $query = new \WP_Query($arguments);
            $pages = $query->get_posts();
            wp_reset_postdata();
            foreach ($pages as $key => $pageId) {
                $link = get_page_link($pageId);
                $pages[$key] = array(
                    'id'   => $pageId,
                    'link' => $link,
                    'path' => trim(parse_url($link, PHP_URL_PATH), '/')
                );
            }
            if (!empty($pages)) {
                $this->cache->set('list', 'Widgetized', $pages);
            }
        } else {
            $pages = $this->cache->get('list', 'Widgetized');
        }

        // check if we are on widgetized page
        $currUrl = trim(parse_url($this->cache->currentUrl(), PHP_URL_PATH), '/');
        foreach ($pages as $page) {
            if (empty($page['path']) && empty($currUrl)) {
                // widgetized front page
                $this->widgetizedpage = true;
            } else if (!empty($page['path']) && strpos($currUrl, $page['path']) === 0) {
                // widgetized page
                $this->widgetizedpage = true;
            }
            if ($this->widgetizedpage === true) {
                $this->pageId = $page['id'];
                break;
            }
        }
        return $pages;
    }

    /**
     * Registers sidebar for every page using our widgetized template
     */
    public function registerSidebars()
    {
        foreach ($this->pages as $page) {
            register_sidebar(array(
                'name'          => 'Sekcje strony "'.get_the_title($page['id']).'"',
                'id'            => $this->getSidebarId($page['id']),
                'description'   => 'Złoż stronę z widżetów.',
                'before_widget' => self::DEFAULT_WRAPER_BEFORE,
                'after_widget'  => self::DEFAULT_WRAPER_AFTER,
                'before_title'  => '',
                'after_title'   => '',
            ));
        }
    }

    /**
     * Register widgetized page widgets
     * @return void
     */
    public function registerWidgets()
    {
        if ($this->widgetizedpage === true || is_admin()) {
            // Pages widgets
            ServiceLocator::get('Widget', 'FeaturedPost');
            ServiceLocator::get('Widget', 'FeaturedPosts');
            ServiceLocator::get('Widget', 'FeaturedCategory');
            ServiceLocator::get('Widget', 'NewsletterBar');
            ServiceLocator::get('Widget', 'Banner');

            // Pages LP widgets
            ServiceLocator::get('Widget', 'LPHero');
            ServiceLocator::get('Widget', 'LPContactMe');
            ServiceLocator::get('Widget', 'LPThreeColumns');
            ServiceLocator::get('Widget', 'LPFourColumns');
            ServiceLocator::get('Widget', 'LPQuote');
            ServiceLocator::get('Widget', 'LPBanner');
        }

        // Register sidebar widgets
        ServiceLocator::get('Widget', 'AskQuestion');
    }

    /**
     * Unregister default widgets
     * @since 2.12.0
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

    /**
     * Add link for quick personalization of Widgetized Page
     * @param string $actions
     * @param object $post
     */
    public function addBuildLink($actions, $post)
    {
        $customizationLink    = get_admin_url(null, '/customize.php');
        $query = http_build_query(array(
            'url' => get_page_link($post->ID)
        ));
        $customizationLink .= '?'.$query;
        $customizeAction = array(
            'customize' => sprintf('<a href="%1$s">Buduj</a>', $customizationLink)
        );
        $actions = $customizeAction + $actions;
        return $actions;
    }

    /**
     * Modify main query for Widgetized Pages only view
     * @return void
     */
    public function filterForView()
    {
        global $typenow;
        if ('page' == $typenow && isset($_GET[self::VIEW_QUERY_VAR]) &&
            self::VIEW_QUERY_VAR_VALUE == $_GET[self::VIEW_QUERY_VAR] ) {
            set_query_var(
                'meta_query',
                array(
                    'relation' => 'AND',
                    array(
                        'key'   => '_wp_page_template',
                        'value' => $this->template,
                    )
                )
            );
        }
    }

    /**
     * Add link to view with only Widgetized Pages
     * @param array $views
     */
    public function addView($views)
    {
        global $typenow;
        $class = '';
        if ('page' == $typenow && isset($_GET[self::VIEW_QUERY_VAR]) &&
            self::VIEW_QUERY_VAR_VALUE == $_GET[self::VIEW_QUERY_VAR] ) {
            $class = 'class="current"';
        }
        $views[self::VIEW_QUERY_VAR_VALUE] = '<a href=\'edit.php?post_type=page&'.self::VIEW_QUERY_VAR.'='.self::VIEW_QUERY_VAR_VALUE.'\' '.$class.'>Landing pages <span class="count">('.intval($this->count).')</span></a>';
        return $views;
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
    public function wrapForReuse($id, $class, $content)
    {
        return implode('', array(
            sprintf(self::DEFAULT_WRAPER_BEFORE, $id, $class),
            $content,
            self::DEFAULT_WRAPER_AFTER
        ));
    }

    /**
     * Returns sidebar ID for this particular page
     * @param  string $pageID
     * @return string
     */
    private function getSidebarId($pageId = false)
    {
        if ($pageId === false) {
            $pageId = $this->pageId;
        }
        return lcfirst($this->getLibrarySlug().'-sidebar-'.$pageId);
    }

    /**
     * Check sidebar id, to determine if it is widgetized page sidebar
     * @param  string  $id
     * @return boolean
     */
    private function isWidgetizedPageSidebarId($id)
    {
        return strpos($id, $this->getLibrarySlug().'-sidebar-') === 0;
    }

    /**
     * Check if it is widgetized page
     * @return bool
     */
    public function isWidgetizedPage()
    {
        return is_page_template($this->template);
    }

    /**
     * Get sidebars data
     * @param  bool|string
     * @return array           Result
     */
    public function getSidebars($filter = 'all')
    {
        // fetch sidebars data
        if (empty($this->sidebars)) {
            $this->sidebars = wp_get_sidebars_widgets();
        }

        // return all sidebars data
        if ($filter == 'all') {
            return $this->sidebars;
        }

        // return active sidebars
        if ($filter == 'active') {
            if (empty($this->activeSidebars)) {
                $this->activeSidebars = $this->sidebars;
                // Don't process inactive widgets
                unset($this->activeSidebars['wp_inactive_widgets']);
                // Don't process orphaned widgets
                foreach ($this->activeSidebars as $sidebar => $widgets) {
                    if (strpos($sidebar, 'orphaned_widgets') === 0) {
                        unset($this->activeSidebars[$sidebar]);
                    }
                }
                // don't process module sidebars not shown on this page
                if (!is_admin()) {
                    // strip module sidebars
                    foreach ($this->activeSidebars as $activeSidebar => $widgets) {
                        if ($this->isWidgetizedPageSidebarId($activeSidebar)) {
                            unset($this->activeSidebars[$activeSidebar]);
                        }
                    }
                    // add module sidebar for this page
                    if ($this->isWidgetizedPage()) {
                        $pageID  = get_queried_object()->ID;
                        $sidebar = $this->getSidebarId($pageID);
                        $widgets = $this->sidebars[$sidebar];
                        $this->activeSidebars[$sidebar] = $widgets;
                    }
                }
            }
            return $this->activeSidebars;
        }

        // return sidebars created by module
        if ($filter == 'module') {
            // find sidebars created by module
            if (empty($this->widgetizedPagesSidebars)) {
                foreach ($this->sidebars as $sidebar => $widgets) {
                    if ($this->isWidgetizedPageSidebarId($sidebar)) {
                        $this->widgetizedPagesSidebars[$sidebar] = $widgets;
                    }
                }
            }
            return $this->widgetizedPagesSidebars;
        }

        return false;
    }

    /**
     * Get choices for Widgetized Page select
     * @since 2.11.0
     * @return array
     */
    public function getSelectChoices()
    {
        $choices = array();
        foreach ($this->pages as $key => $page) {
            $choices[get_page_link($page['id'])] = get_the_title($page['id']);
        }
        return $choices;
    }

    /**
     * Get widget area
     * @return void
     */
    public function getWidgetArea()
    {
        // return false, do not cache
        // WORKAROUND: randomly no widgets template gets cached
        if (!is_active_sidebar($this->getSidebarId())) {
            return false;
        }
        ob_start();
        if (!dynamic_sidebar($this->getSidebarId())) {
            $template = ServiceLocator::getNew('Component', 'Template');
            $template($this->getTemplatesDirectory().'/NoWidgets.php');
            $template->librarySlug = $this->getLibrarySlug();
            echo $template;
        }
        return ob_get_clean();
    }

    /**
     * Shows appropriate sidebar for page
     */
    public function render()
    {
        echo $this->cache->cacheHtml(
            $this->getCacheId(),
            $this->getModuleName(),
            array($this, 'getWidgetArea')
        );
    }


    /**
     * CACHING METHODS
     */

    /**
     * Return id of cache entry
     * @since  2.12.0
     * @return string
     */
    private function getCacheId($id = false)
    {
        if ($id) {
            $sidebar = $this->getSidebarId($id);
        } else {
            $sidebar = $this->getSidebarId($this->pageId);
        }
        return $sidebar;
    }

    /**
     * Clear cache for all sidebars
     * @since  2.12.9
     * @return void
     */
    private function clearSidebarsCache($sidebarId = false)
    {
        if ($sidebarId) {
            $this->cache->delete($sidebarId, $this->getModuleName());
        } else {
            $this->cache->forcePurge($this->getModuleName());
        }
    }

    /**
     * WordPress filter. Trigger widgetized page cache clear on widget update
     * @since  2.12.0
     * @param  array $instance
     * @param  array $newInstance
     * @param  array $oldInstance
     * @param  object $widget
     * @return array
     */
    public function filterWidgetUpdate($instance, $newInstance, $oldInstance, $widget)
    {
        $this->clearSidebarsCache();
        return $newInstance;
    }

    /**
     * Find in which sidebar this particular widget id is
     * @since  2.12.0
     * @param  string $widgetId
     * @return string|bool
     */
    private function findWidgetSidebar($search)
    {
        $sidebars = $this->getSidebars('module');
        foreach ($sidebars as $sidebarId => $widgets) {
            foreach ($widgets as $widgetId) {
                if ($widgetId == $search) {
                    return $sidebarId;
                }
            }
        }
        return false;
    }

    /**
     * WordPress action. Check if we need to clear sidebar cache after customizer update
     * @since  2.12.0
     * @param  \WP_Customize_Manager $object
     * @return void
     */
    public function actionCustomizeSave($object)
    {
        foreach ($object->unsanitized_post_values() as $key => $value) {
            if (strpos($key, 'widget_'.$this->getLibrarySlug()) === 0) {
                // look for changed settings for our widgets
                // change post var name to widget id
                $widgetId = substr($key, strlen('widget_'));
                $widgetId = trim(str_replace(array('[', ']'), '-', $widgetId), '-');
                // find where updated widget acctualy is
                $sidebar[] = $this->findWidgetSidebar($widgetId);
            } else if (strpos($key, 'sidebars_widgets['.$this->getLibrarySlug()) === 0) {
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
                $this->clearSidebarsCache($sidebarId);
            }
        }
    }

    /**
     * WordPress action. Clear pages cache on post save/delete
     * @param  int $postId
     * @return void
     */
    public function actionClearCache($postId)
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

        $this->pages = $this->getPages();
        $this->clearSidebarsCache();
    }
}
