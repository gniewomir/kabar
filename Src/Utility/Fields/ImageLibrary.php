<?php
/**
 * Image upload field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.28.7
 * @package    kabar
 * @subpackage fields
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Handles redering & updating image uploads field
 */
class ImageLibrary extends Image
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
     * Help text for a field
     * @var string
     */
    protected $help;

    /**
     * Show image preview?
     * @var bool
     */
    protected $preview;

    /**
     * Adds media uploader scripts
     */
    public function addScripts()
    {
        wp_enqueue_media();
        wp_enqueue_script(
            $this->getLibrarySlug().'-image-library-field-script',
            $this->getAssetsUri().'js/ImageLibrary.js',
            array('media-upload', 'thickbox'),
            $this->getLibraryVersion(),
            self::IN_FOOTER
        );
    }

    /**
     * Render field
     * @return \kabar\Utility\Template\Template
     */
    public function render()
    {
        $template = parent::render();
        $template->image = wp_get_attachment_image_src($template->value, 'medium')[0];
        return $template;
    }

    /**
     * Get field value
     * @return integer
     */
    public function get()
    {
        return (integer) $this->storage->retrieve($this->getSlug());
    }

    /**
     * Save new field value
     * @return integer
     */
    public function save()
    {
        // Sanitize user input.
        $value = (integer) $this->storage->updated($this->getSlug());

        // store value
        $this->storage->store($this->getSlug(), $value);

        return $value;
    }
}
