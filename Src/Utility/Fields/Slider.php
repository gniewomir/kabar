<?php
/**
 * Slider field
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
class Slider extends AbstractField
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
     * @var integer
     */
    protected $default;

    /**
     * Minimum value
     * @var integer
     */
    protected $min;

    /**
     * Maximum value
     * @var integer
     */
    protected $max;

    /**
     * Change step
     * @var integer
     */
    protected $step;

    /**
     * Setup slider field
     * @param string  $slug
     * @param string  $title
     * @param integer $min
     * @param integer $max
     * @param integer $step
     * @param integer $default
     */
    public function __construct($slug, $title, $min, $max, $step, $default)
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->default  = $default;

        $this->min      = $min;
        $this->max      = $max;
        $this->step     = $step;

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
     * Adds field scripts
     */
    public function addScripts()
    {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script(
            $this->getLibrarySlug().'-metabox-slider-script',
            $this->getAssetsUri().'js/Slider.js',
            array('jquery-ui-core', 'jquery-ui-slider'),
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
        $template           = $this->getTemplate();
        $template->id       = $this->storage->getFieldId($this->getSlug());
        $template->cssClass = $this->getCssClass();
        $template->title    = $this->title;

        $value = $this->get();
        $data = array(
            'id'      => $this->storage->getFieldId($this->getSlug()),
            'min'     => $this->min,
            'max'     => $this->max,
            'step'    => $this->step,
            'val'     => $value,
        );
        wp_localize_script(
            $this->getLibrarySlug().'-metabox-slider-script',
            str_replace('-', '_', $this->storage->getFieldId($this->getSlug())),
            $data
        );
        $template->value = empty($value) ? $this->default : $value;
        return $template;
    }

    /**
     * Get field value
     * @return string
     */
    public function get()
    {
        $value = $this->storage->retrieve($this->getSlug());

        // Sanitize output
        if (!is_numeric($value)) {
            $value = $this->default;
        }
        if ($value > $this->max) {
            $value = $this->default;
        }
        if ($value < $this->min) {
            $value = $this->default;
        }

        return $value;
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

        // Sanitize user input
        $value = $this->storage->updated($this->getSlug());

        if (!is_numeric($value)) {
            $value = $this->default;
        }
        if ($value > $this->max) {
            $value = $this->default;
        }
        if ($value < $this->min) {
            $value = $this->default;
        }

        // store value
        $this->storage->store($this->getSlug(), $value);

        return $value;
    }
}
