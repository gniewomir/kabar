<?php
/**
 * Providing admin interface for widgetized pages
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.17.0
 * @package    kabar
 * @subpackage modules
 */

namespace kabar\Module\Pages;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Allows creation of pages constructed from widgets
 */
class AdminInterface
{

    const VIEW_QUERY_VAR           = 'filter';
    const VIEW_QUERY_VAR_VALUE     = 'widgetized';

    /**
     * Template used for widgetized pages, path relative to theme directory
     * @var string
     */
    protected $template;

    /**
     * List of widgetized pages
     * @var array
     */
    protected $pages;


    /**
     * Setup admin interface for widgetized pages
     * @param string $template
     * @param array $pages
     */
    public function __construct($template, $pages)
    {
        $this->template = $template;
        $this->pages    = $pages;

        // Add link to particular page customization
        add_filter('page_row_actions', array($this, 'addBuildLink'), 10, 2);

        // Add separate view for widgetized pages in pages section of wp-admin
        add_filter('pre_get_posts', array($this, 'filterForView'));
        add_filter('views_edit-page', array($this, 'addView'));
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
        $count = count($this->pages);
        $views[self::VIEW_QUERY_VAR_VALUE] = '<a href=\'edit.php?post_type=page&'.self::VIEW_QUERY_VAR.'='.self::VIEW_QUERY_VAR_VALUE.'\' '.$class.'>Landing pages <span class="count">('.intval($count).')</span></a>';
        return $views;
    }
}
