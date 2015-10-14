<?php
/**
 * Show other fields field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.24.4
 * @package    kabar
 * @subpackage fields
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Submit button field
 */
class Show extends Checkbox
{
    const IN_FOOTER = true;

    /**
     * Setup text field
     * @param string $slug
     * @param string $title
     * @param bool   $default
     * @param string $help
     */
    public function __construct($slug, $title, $default = false, $help = '')
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->default  = $default;
        $this->help     = $help;

        $this->value    = self::ENABLED;

        add_action('admin_enqueue_scripts', array($this, 'addScripts'));
    }

    /**
     * Adds media uploader scripts
     */
    public function addScripts()
    {
        wp_enqueue_media();
        wp_enqueue_script(
            $this->getLibrarySlug().'-show-field-script',
            $this->getAssetsUri().'js/Show.js',
            array(),
            $this->getLibraryVersion(),
            self::IN_FOOTER
        );
    }
}