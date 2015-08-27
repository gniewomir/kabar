<?php
/**
 * Scripts module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.18.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\Scripts;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Scripts module main class
 */
class Scripts extends \kabar\Module\Module\Module
{

    /**
     * Data passed to our scripts
     * @var array
     */
    protected $data;

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
        if (did_action('after_setup_theme')) {
            trigger_error('Module "'.$this->getModuleName().'" have to be setup before "after_setup_theme" action.', E_USER_ERROR);
        }

        $this->cache = ServiceLocator::get('Module', 'Cache');

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
    public function addScriptData($name, $value)
    {
        if (isset($this->data[$name])) {
            $this->data[$name] = (object) array_merge((array) $this->data[$name], (array) $value);
            return;
        }
        $this->data[$name] = (object) $value;
    }

    /**
     * Return scripts data
     * @return \kabar\Component\Template\Template
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

        $template = ServiceLocator::getNew('Component', 'Template');
        $template($this->getTemplatesDirectory().'Scripts.php');
        $template->data = $scriptsData;
        $template->slug = $this->getLibrarySlug();
        return $template;
    }

    /**
     * Get id for cache entry
     * @return string
     */
    public function getCacheId()
    {
        return $this->getLibraryVersion();
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
}
