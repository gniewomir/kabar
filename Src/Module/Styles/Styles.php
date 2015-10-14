<?php
/**
 * Inline Styles module
 *
 * @package    kabar
 * @subpackage Modules
 * @since      0.0.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Module\Styles;

/**
 * Allows collecting inline style from other modules, and outputing them where needed
 */
final class Styles extends \kabar\Module\Module\Module
{

    /**
     * Inline styles
     * @var array
     */
    private $styles;

    /**
     * Cache object
     * @var \kabar\Module\Cache\Cache
     */
    private $cache;

    /**
     * Template factory
     * @since 0.38.0
     * @var \kabar\Factory\Template\Template
     */
    private $templateFactory;

    // INTERFACE

    /**
     * Setup styles module
     * @param \kabar\Factory\Template\Template $templateFactory
     * @param \kabar\Module\Cache\Cache        $cache
     */
    public function __construct(\kabar\Factory\Template\Template $templateFactory, \kabar\Module\Cache\Cache $cache)
    {
        $this->requireBeforeAction('after_setup_theme');

        $this->templateFactory = $templateFactory;
        $this->cache           = $cache;

        /**
         * Deprecated functionality
         * @deprecated deprecated since version 2.38.0
         */
        $this->deprecated();
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
     * @return \kabar\Utility\Template\Template
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
            $template = $this->templateFactory->create();
            $template($this->getTemplatesDirectory().'Styles.php');
            $template->slug   = $this->getLibrarySlug();
            $template->styles = $styles;
            return $template;
        }
    }

    /**
     * Clear styles cache
     * @since  0.12.9
     * @return void
     */
    public function clearCache()
    {
        $this->cache->forcePurge($this->getModuleName());
    }

    /**
     * Echo inline styles
     * @return void
     */
    public function render()
    {
        echo $this->cache->cacheHtml(
            $this->getCacheId(),
            $this->getModuleName(),
            array($this, 'getStyles')
        );
    }

    // INTERNAL

    /**
     * Get id for cache entry
     * @return string
     */
    private function getCacheId()
    {
        return rtrim($this->cache->getCurrentUrl(), '/');
    }

    /**
     * Deprecated functionality
     * @deprecated deprecated since version 2.38.0
     * @since      0.38.0
     * @return     void
     */
    private function deprecated()
    {
        // if we don't have cached inline styles force other modules to skip cache
        if (!$this->cache->isCached($this->getCacheId(), $this->getModuleName())) {
            $this->cache->startPurge();
        }

        /**
         * @deprecated deprecated since version 2.35.1
         *
         * Form component allows for adding form update callbacks. Which should be used instead.
         */
        add_action('save_post', array($this, 'clearCache'), 8, 1);
        add_action('delete_post', array($this, 'clearCache'), 8, 1);
    }
}
