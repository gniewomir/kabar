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

    /**
     * Config module
     * @since 2.17.0
     * @var \kabar\Module\Styles\Styles
     */
    protected $styles;

    /**
     * Cache module
     * @since 2.12.0
     * @var \kabar\Module\Cache\Cache
     */
    protected $cache;

    /**
     * Sidebars module
     * @since 2.17.0
     * @var \kabar\Module\Sidebars\Sidebars
     */
    protected $sidebars;

    /**
     * Widget modules callbacks
     * @var array
     */
    protected $widgets;

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
     * Widgetized Pages Sidebars and widgets data
     * @var array
     */
    protected $widgetizedPagesSidebars;

    /**
     * Current page id
     * @var bool
     */
    protected $pageId;

    /**
     * Setup widgetized pages module
     * @param \kabar\Module\Styles\Styles     $styles       Required, to make sure that will be set up for widgets
     * @param \kabar\Module\Cache\Cache       $cache
     * @param \kabar\Module\Sidebars\Sidebars $sidebars
     * @param array                           $widgets      Array of module callbacks
     * @param string                          $pageTemplate
     */
    public function __construct(
        \kabar\Module\Styles\Styles     $styles,
        \kabar\Module\Cache\Cache       $cache,
        \kabar\Module\Sidebars\Sidebars $sidebars,
        array $widgets = array(),
        $pageTemplate = 'templates/widgetized-page.php'
    ) {
        $this->requireBeforeAction('after_setup_theme');

        // Dependencies
        $this->styles   = $styles;
        $this->cache    = $cache;
        $this->sidebars = $sidebars;

        // Widget callbacks
        $this->widgets = $widgets;

        // Page tamplate path, relative to theme directory
        $this->template = $pageTemplate;

        // Find widgetized pages
        $this->pages = $this->getWidgetizedPagesList();

        // register sidebars
        $this->registerSidebars();

        // register widgets
        $this->registerWidgets();

        // admin interface
        if (is_admin()) {
            new AdminInterface($this->template, $this->pages);
        }
    }

    /**
     * Check if we are on widgetized page
     * @return boolean
     */
    public function isWidgetizedPage()
    {
        return (bool) $this->getWidgetizedPageId();
    }

    /**
     * Get widgetized pages list
     * @since 2.12.19
     * @return array
     */
    protected function getWidgetizedPagesList()
    {
        if (isset($this->pages)) {
            return $this->pages;
        }

        if (!$this->cache->isCached('list', $this->getModuleName())) {
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
                    'path' => trim(parse_url($link, PHP_URL_PATH), '/')
                );
            }
            if (!empty($pages)) {
                $this->cache->set('list', $this->getModuleName(), $pages);
            }
        } else {
            $pages = $this->cache->get('list', $this->getModuleName());
        }

        return $pages;
    }

    /**
     * Get current widgetized page id
     * @return false|string
     */
    protected function getWidgetizedPageId()
    {
        // we already know the id
        if (isset($this->pageId)) {
            return $this->pageId;
        }

        // get widgetized pages
        $pages = $this->getWidgetizedPagesList();

        // check if current url matches url of any widgetized page
        $currUrl = trim(parse_url($this->cache->currentUrl(), PHP_URL_PATH), '/');
        foreach ($pages as $page) {
            if (empty($page['path']) && empty($currUrl)) {
                // yes, widgetized front page
                $this->pageId = $page['id'];
                return $this->pageId;
            } else if (!empty($page['path']) &&
                       strpos($currUrl, $page['path']) === 0 &&
                       strpos($currUrl, $page['path'].'/') !== 0) { // make sure that we dont confuse category slug with page name
                // yes, other widgetized page
                $this->pageId = $page['id'];
                return $this->pageId;
            }
        }

        // not a widgetized page, no page id
        return false;
    }

    /**
     * Returns sidebar id this particular widgetized page or provided page id
     * @param  string $pageId
     * @return string
     */
    protected function getSidebarId($pageId = false)
    {
        if ($pageId === false) {
            $pageId = $this->getWidgetizedPageId();
        }
        if ($pageId === false) {
            trigger_error('You have to provide page id outside widgetized pages.', E_USER_ERROR);
        }
        return lcfirst($this->getLibrarySlug().'-sidebar-'.$pageId);
    }

    /**
     * Registers sidebar for every page using our widgetized template
     */
    protected function registerSidebars()
    {
        // register all our sidebars
        if (is_admin()) {
            $pages = $this->getWidgetizedPagesList();
            foreach ($pages as $page) {
                $this->sidebars->register(
                    array(
                        'name'          => 'Page "'.get_the_title($page['id']).'" sections',
                        'id'            => $this->getSidebarId($page['id']),
                        'description'   => __('Assembly this page from widgets.', $this->getLibrarySlug()),
                        'before_widget' => self::DEFAULT_WRAPER_BEFORE,
                        'after_widget'  => self::DEFAULT_WRAPER_AFTER,
                        'before_title'  => '',
                        'after_title'   => '',
                    )
                );
            }
            return;
        }

        // no need to register any of our sidebars
        if (!$this->isWidgetizedPage()) {
            return;
        }

        // register current page sidebar
        $this->sidebars->register(
            array(
                'name'          => 'Page "'.get_the_title($this->getWidgetizedPageId()).'" sections',
                'id'            => $this->getSidebarId($this->getWidgetizedPageId()),
                'description'   => __('Assembly this page from widgets.', $this->getLibrarySlug()),
                'before_widget' => self::DEFAULT_WRAPER_BEFORE,
                'after_widget'  => self::DEFAULT_WRAPER_AFTER,
                'before_title'  => '',
                'after_title'   => '',
            )
        );
    }

    /**
     * Register widgets
     * @return void
     */
    protected function registerWidgets()
    {
        if (!is_admin() && !$this->isWidgetizedPage()) {
            return;
        }
        foreach ($this->widgets as $widget) {
            ServiceLocator::get(array_shift($widget), array_shift($widget));
        }
    }

    /**
     * Shows appropriate sidebar for page
     */
    public function render()
    {
        $this->sidebars->render($this->getSidebarId());
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
}
