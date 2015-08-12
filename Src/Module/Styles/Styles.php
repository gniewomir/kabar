<?php
/**
 * Inline Styles module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\Styles;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Styles module main class
 */
class Styles extends \kabar\Module\Module\Module
{

    /**
     * Inline styles
     * @var array
     */
    protected $styles;

    /**
     * Cache object
     * @var object
     */
    protected $cache;

    /**
     * Setup
     */
    public function __construct()
    {
        $this->cache = ServiceLocator::get('Module', 'Cache');
        if (!$this->cache->isCached($this->getCacheId(), $this->getModuleName())) {
            $this->cache->startPurge();
        }
        add_action('save_post', array($this, 'clearStylesCache'), 8, 1);
        add_action('delete_post', array($this, 'clearStylesCache'), 8, 1);
    }

    /**
     * Add inline style
     * @param string $selector
     * @param array  $properties
     */
    public function add($selector, $properties)
    {
        if (isset($this->styles[$selector])) {
            $this->styles[$selector] = array_merge($this->styles[$selector], $properties);
        } else {
            $this->styles[$selector] = $properties;
        }
    }

    /**
     * Return styles html tag
     * @return \kabar\Component\Template\Template
     */
    public function getStyles()
    {
        if (empty($this->styles)) {
            return;
        }
        $styles = '';
        foreach ($this->styles as $selector => $properties) {
            $parsedProperties = '';
            foreach ($properties as $property => $value) {
                $parsedProperties .= $property.':'.$value.';';
            }
            if ($parsedProperties) {
                $styles .= $selector.' {'.$parsedProperties.'} ';
            }
        }
        if ($styles) {
            $template = ServiceLocator::getNew('Component', 'Template');
            $template($this->getTemplatesDirectory().'Styles.php');
            $template->styles = $styles;
            return $template;
        }
    }

    /**
     * Clear cache for all sidebars
     * @since  2.12.9
     * @return void
     */
    public function clearStylesCache()
    {
        $this->cache->forcePurge($this->getModuleName());
    }

    /**
     * Get id for cache entry
     * @return string
     */
    public function getCacheId()
    {
        return rtrim($this->cache->currentUrl(), '/');
    }

    /**
     * Echo inline styles
     * @return void
     */
    public function render()
    {
        echo $this->cache->cacheHtml($this->getCacheId(), $this->getModuleName(), array($this, 'getStyles'));
    }
}
