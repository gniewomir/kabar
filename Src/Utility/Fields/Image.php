<?php
/**
 * Image upload field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Handles redering & updating image uploads field
 */
class Image extends AbstractField
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
     * Setup text field
     * @param string $slug
     * @param string $title
     * @param string $default
     * @param string $help
     */
    public function __construct($slug, $title, $default = '', $help = '', $preview = false)
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->default  = $default;
        $this->help     = $help;
        $this->preview  = $preview;
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
     * Adds media uploader scripts
     */
    public function addScripts()
    {
        wp_enqueue_media();
        wp_enqueue_script(
            $this->getLibrarySlug().'-media-upload-script',
            $this->getAssetsUri().'js/Image.js',
            array('media-upload', 'thickbox'),
            $this->getLibraryVersion(),
            self::IN_FOOTER
        );
    }

    /**
     * Render text
     * @return \kabar\Component\Template\Template
     */
    public function render()
    {
        $template                 = $this->getTemplate();
        $template->id             = $this->storage->getFieldId($this->getSlug());
        $template->cssClass       = $this->getCssClass();

        $fieldClass               = explode(' ', $template->cssClass);
        $fieldClass               = end($fieldClass);
        $template->buttonCssClass = $fieldClass.'-button';

        $template->title          = $this->title;
        $template->help           = $this->help;
        $template->preview        = $this->preview;
        $value                    = $this->get();
        $template->value          = empty($value) ? $this->default : $value;
        return $template;
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
     * @return mixed
     */
    public function save()
    {
        if (is_null($this->storage->updated($this->getSlug()))) {
            return;
        }

        // Sanitize user input.
        $value = esc_url_raw($this->storage->updated($this->getSlug()), array('http', 'https'));

        // store value
        $this->storage->store($this->getSlug(), $value);

        return $value;
    }
}
