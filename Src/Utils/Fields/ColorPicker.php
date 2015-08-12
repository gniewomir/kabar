<?php
/**
 * ColorPicker field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */
namespace kabar\Utils\Fields;

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
     * @var stiring
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
     * Setup text field
     * @param string $slug
     * @param string $title
     * @param string $default
     */
    public function __construct($slug, $title, $default = '#FFFFFF')
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->default  = $default;
        $this->template = $this->getTemplatesDir().'ColorPicker.php';


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
     * @return /kabar/Component/Template/Template
     */
    public function render()
    {
        $template = ServiceLocator::getNew('Component', 'Template');
        $template($this->template);
        $template->id          = $this->storage->getFieldId($this->getSlug());
        $template->cssClass    = $this->getCssClass();
        $template->librarySlug = $this->getLibrarySlug();
        $template->title       = $this->title;
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
        return $this->storage->retrieve($this->getSlug());
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
