<?php

namespace kabar\Widget\Widget\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Abstract widget field.
 *
 * @internal
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      1.0.0
 * @package    kabar
 * @subpackage kabar_widget_fields_api
 */
abstract class AbstractField
{
    const TEMPLATES_DIRECTORY = 'Templates';
    const ASSETS_DIRECTORY    = 'assets';

    const TEMPLATE_EXTENSION  = '.php';

    /**
     * WordPress widget instace
     * @var \WP_Widget
     */
    protected $widgetInstance;

    /**
     * ID/Name suffix
     * @var string
     */
    protected $suffix = '';

    /**
     * Templates directory
     * @since 2.16.0
     * @var string
     */
    protected $templatesDirectory;

    /**
     * Assets uri
     * @since 2.16.0
     * @var string
     */
    protected $assetsUri;

    /**
     * Binds field instance to WordPress widget
     * @since 2.0.0
     * @param WP_Widget $widget  WordPress widget instance
     */
    public function bindToWidget(\WP_Widget $widget)
    {
        $this->widgetInstance = $widget;
    }

    /**
     * Set field name/ID suffix
     * @param string $suffix
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;
    }

    /**
     * Returns rendered field value
     * @param  array $args     Widget arguments.
     * @param  array $instance Saved values from database.
     * @return mixed
     */
    abstract public function get($args, $instance);

    /**
     * Field rendering for back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return kabar\Component\Template\Template
     */
    public function form($instance)
    {
        $template = ServiceLocator::getNew('Component', 'Template');
        $template($this->getTemplatesDirectory().$this->getFieldType().self::TEMPLATE_EXTENSION);
        $template->fieldId   = $this->getWordPressFieldId();
        $template->fieldName = $this->getWordPressFieldName();
        $template->cssClass  = $this->getCssClass();
        return $template;
    }

    /**
     * Sanitize widget field values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $newInstance Values just sent to be saved.
     * @param array $oldInstance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    abstract public function update($newInstance, $oldInstance);

    /**
     * Returns field id
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns WordPress id for this field
     * @return string
     */
    protected function getWordPressFieldId()
    {
        if ($this->suffix) {
            $suffix = str_replace(array('[', ']'), '-', $this->suffix);
            $suffix = rtrim($suffix, '-');
        } else {
            $suffix = $this->suffix;
        }
        return $this->widgetInstance->get_field_id($this->id).$suffix;
    }

    /**
     * Returns WordPress name for this field
     * @return string
     */
    protected function getWordPressFieldName()
    {
        return $this->widgetInstance->get_field_name($this->id).$this->suffix;
    }

    /**
     * Returns field type
     * @return string
     */
    public function getFieldType()
    {
        $class = explode('\\', get_class($this));
        $type  = end($class);

        return $type;
    }

    /**
     * Returns slug for main library
     * @return string
     */
    protected function getLibrarySlug()
    {
        return ServiceLocator::VENDOR_NAMESPACE;
    }

    /**
     * Returns version of library
     * @return string
     */
    public function getLibraryVersion()
    {
        return ServiceLocator::VERSION;
    }

    /**
     * Returns uri of fields assets directory
     * @return string
     */
    protected function getAssetsUri()
    {
        if (empty($this->assetsUri)) {
            $this->assetsUri = plugins_url('', __FILE__).'/'.self::ASSETS_DIRECTORY.'/';
        }
        return $this->assetsUri;
    }

    /**
     * Returns default templates directory
     * @return string
     */
    protected function getTemplatesDirectory()
    {
        if (empty($this->templatesDirectory)) {
            $this->templatesDirectory = __DIR__.DIRECTORY_SEPARATOR.self::TEMPLATES_DIRECTORY.DIRECTORY_SEPARATOR;
        }
        return $this->templatesDirectory;
    }

    /**
     * Returns css class
     * @return string
     */
    protected function getCssClass()
    {
        $class                 = explode('\\', get_class($this));
        $fieldTypeCssClass     = implode('-', $class);
        array_pop($class);
        $fieldsCssClass        = implode('-', $class);
        return $fieldsCssClass.' '.$fieldTypeCssClass;
    }

    /**
     * Do shortcodes including 'embed'
     * @see http://wordpress.stackexchange.com/a/23213
     * @param  string $content
     * @return string
     */
    protected function doShortcodes($content)
    {
        global $wp_embed;
        $content = $wp_embed->run_shortcode($content);
        $content = do_shortcode($content);

        return $content;
    }

    /**
     * Returns name for data object used to localize/pass data to script via wp_localize_script
     * @see https://codex.wordpress.org/Function_Reference/wp_localize_script
     * @return string
     */
    protected function getJavaScriptDataObjectName()
    {
        return str_replace('-', '_', $this->getWordPressFieldId());
    }
}
