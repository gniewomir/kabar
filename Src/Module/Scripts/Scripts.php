<?php
/**
 * Scripts module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.18.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\Scripts;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Scripts module main class
 */
class Scripts extends \kabar\Module
{

    /**
     * Data passed to our scripts
     * @var array
     */
    protected $data;

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
     * Setup scripts module
     * @param \kabar\Factory\Template\Template $templateFactory
     * @param \kabar\Module\Cache\Cache        $cache
     */
    public function __construct(\kabar\Factory\Template\Template $templateFactory, \kabar\Module\Cache\Cache $cache)
    {
        $this->templateFactory = $templateFactory;
        $this->cache           = $cache;

        // if we don't have cached inline styles force other modules to skip cache
        if (!$this->cache->isCached($this->getCacheId(), $this->getModuleName())) {
            $this->cache->startPurge();
        }
    }

    /**
     * Add script data
     * @param  string $name
     * @param  array  $value
     * @return void
     */
    public function addData($name, $value)
    {
        if (isset($this->data[$name])) {
            $this->data[$name] = (object) array_merge((array) $this->data[$name], (array) $value);
            return;
        }
        $this->data[$name] = (object) $value;
    }

    /**
     * Return scripts data
     * @return \kabar\Utility\Template\Template
     */
    public function getScripts()
    {
        if (empty($this->data)) {
            return;
        }

        $scriptsData = array();
        $scriptsData[] = 'var '.$this->getLibrarySlug().' = '.$this->getLibrarySlug().' || {};';
        $scriptsData[] = $this->getLibrarySlug().'.'.$this->getModuleName().' = {};';
        foreach ($this->data as $name => $data) {
            foreach ($data as $key => $value) {
                if (!is_scalar($value)) {
                    continue;
                }
                $data->$key = html_entity_decode((string) $value, ENT_QUOTES, 'UTF-8');
            }
            $scriptsData[] = $this->getLibrarySlug().'.'.$this->getModuleName().'.'.$name.' = '.wp_json_encode($data).';';
        }

        $template = $this->templateFactory->create();
        $template($this->getTemplatesDirectory().'Scripts.php');
        $template->data = $scriptsData;
        $template->slug = $this->getLibrarySlug();
        return $template;
    }

    /**
     * Clear styles cache
     * @since  0.38.0
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
            array($this, 'getScripts')
        );
    }

    // INTERNAL

    /**
     * Get id for cache entry
     * @return string
     */
    private function getCacheId()
    {
        return $this->cache->getCurrentUrl();
    }
}
