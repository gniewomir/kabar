<?php
/**
 * Sidebars module
 *
 * @package    kabar
 * @subpackage module
 * @since      0.17.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Module\Sidebars;

/**
 * Register and render WordPress sidebars
 */
class Sidebars extends \kabar\Module
{
    /**
     * Template factory
     * @since 0.38.0
     * @var \kabar\Factory\Template\Template
     */
    private $templateFactory;

    /**
     * Config module
     * @var \kabar\Module\Config\Config
     */
    private $config;

    /**
     * Cache module
     * @var \kabar\Module\Cache\Cache
     */
    private $cache;

    /**
     * Sidebars and widgets data
     * @var array
     */
    private $sidebars;

    /**
     * Cache cleaning object
     * @var \kabar\Module\Sidebars\Cleaner
     */
    private $cleaner;

    // INTERFACE

    /**
     * Setup sidebars module
     * @param \kabar\Factory\Template\Template $templateFactory
     * @param \kabar\Module\Config\Config      $config
     * @param \kabar\Module\Cache\Cache        $cache
     */
    public function __construct(
        \kabar\Factory\Template\Template $templateFactory,
        \kabar\Module\Config\Config $config,
        \kabar\Module\Cache\Cache $cache
    ) {
        $this->templateFactory = $templateFactory;
        $this->config          = $config;
        $this->cache           = $cache;

        // if in admin - initialize cache cleaner
        if (is_admin()) {
            $this->cleaner = new Cleaner($this);
        }

        $moduleName = $this->getModuleName();

        $this->config->registerSection(
            $moduleName,
            array(
                'sectionTitle'          => __('Sidebars', $this->getLibrarySlug()),
                'sectionCapability'     => 'update_core',
                'disableDefaultWidgets' => array(
                    'type'    => 'checkbox',
                    'default' => true,
                    'label'   => __('Disable default WordPress widgets', $this->getLibrarySlug()),
                )
            )
        );

        // unregister default widgets
        if ($this->config->$moduleName->disableDefaultWidgets) {
            add_action('widgets_init', array($this, 'unregisterDefaultWidgets'), 11);
        }
        // Add widgetized pages sidebars and widgets
        $this->requireBeforeAction('widgets_init');
        add_action('widgets_init', array($this, 'registerSidebars'), 9);
    }

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
     * @param string $id
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
     * Callback. Get widget area
     * @internal
     * @return string
     */
    public function getSidebar($id)
    {
        ob_start();
        if (!dynamic_sidebar($id)) {
            $template = $this->templateFactory->create();
            $template($this->getTemplatesDirectory().'/NoWidgets.php');
            $template->librarySlug = $this->getLibrarySlug();
            echo $template;
        }
        return ob_get_clean();
    }

    /**
     * WordPress action. Registers all added sidebars with WordPress.
     * @internal
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
    }

    /**
     * WordPress action. Unregister default widgets
     * @internal
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
