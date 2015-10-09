<?php
/**
 * ColorPicker field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */
namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Color picker field class
 */
class ColorPicker extends AbstractField
{

    const IN_FOOTER = true;

    /**
     * Field slug
     * @var string
     */
    protected $slug;

    /**
     * Field title
     * @var string
     */
    protected $title;

    /**
     * Field default value
     * @var string
     */
    protected $default;

    /**
     * Field template file path
     * @var string
     */
    protected $template;

    /**
     * Help text
     * @since 2.34.0
     * @var   string
     */
    protected $help;

    /**
     * Setup text field
     * @param string $slug
     * @param string $title
     * @param string $default
     * @param string $help
     */
    public function __construct($slug, $title, $default = '#FFFFFF', $help = '')
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->default  = $default;
        add_action('admin_enqueue_scripts', array($this, 'addScripts'));
    }

    /**
     * Removes this field
     * @return void
     */
    public function remove()
    {
        remove_action('admin_enqueue_scripts', array($this, 'addScripts'));
    }

    /**
     * Adds color picker scripts
     */
    public function addScripts()
    {
        /**
         * @link http://www.dematte.at/tinyColorPicker/
         * @link https://github.com/PitPik/tinyColorPicker
         */
        wp_enqueue_script(
            'jquery-color-picker',
            $this->getAssetsUri().'js/vendor/jqColorPicker.min.js',
            array(),
            $this->getLibraryVersion(),
            self::IN_FOOTER
        );
        wp_enqueue_script(
            $this->getLibrarySlug().'-color-picker',
            $this->getAssetsUri().'js/ColorPicker.js',
            array('jquery-color-picker'),
            $this->getLibraryVersion(),
            self::IN_FOOTER
        );
    }

    /**
     * Render field
     * @return \kabar\Component\Template\Template
     */
    public function render()
    {
        $template              = $this->getTemplate();
        $template->id          = $this->storage->getPrefixedKey($this->getSlug());
        $template->cssClass    = $this->getCssClass();
        $template->librarySlug = $this->getLibrarySlug();
        $template->title       = $this->title;
        $template->help        = $this->help;
        $value                 = $this->get();
        $template->value       = empty($value) ? $this->default : $value;
        return $template;
    }

    /**
     * Checks if provided value is valid color
     * @param  string $value
     * @return boolean
     */
    public function isValidHexColor($value)
    {
        if (preg_match('/^#[a-f0-9]{6}$/i', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Get field value
     * @return string
     */
    public function get()
    {
        $value = $this->storage->retrieve($this->getSlug());

        if (!empty($value)) {
            return $value;
        }

        return $this->default;
    }

    /**
     * Save new field value
     * @return string
     */
    public function save()
    {
        if (is_null($this->storage->updated($this->getSlug()))) {
            return;
        }

        if ($this->isValidHexColor($this->storage->updated($this->getSlug()))) {
            $value = $this->storage->updated($this->getSlug());
        } else {
            $value = $this->default;
        }

        // Sanitize user input.
        $value = sanitize_text_field($value);

        // store value
        $this->storage->store($this->getSlug(), $value);

        return $value;
    }
}
