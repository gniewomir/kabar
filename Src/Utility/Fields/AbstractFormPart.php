<?php
/**
 * Base class for form parts that are not typical fields
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 * @see        https://codex.wordpress.org/Function_Reference/add_meta_box
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Form part abstraction and utility functions
 */
abstract class AbstractFormPart implements InterfaceFormPart
{
    /**
     * Field slug
     * @var string
     */
    protected $slug;

    /**
     * Field type
     * @since 2.24.4
     * @var string
     */
    private $fieldType;

    /**
     * Field template directory
     * @since 2.24.4
     * @var string
     */
    private $templateDirectory;

    /**
     * Assets directory uri
     * @var string
     */
    protected $assetsUri;

    /**
     * Library assets uri
     * @since 2.26.4
     * @var   string
     */
    protected $libraryAssetsUri;

    /**
     * Render field
     * @return \kabar\Utility\Template\Template
     */
    abstract public function render();

    /**
     * Returns field slug
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Returns field css class
     * @return string
     */
    public function getCssClass()
    {
        $class          = explode('\\', get_class($this));
        $fieldCssClass  = implode('-', $class);
        array_pop($class);
        $fieldsCssClass = implode('-', $class);

        return $fieldsCssClass.' '.$fieldCssClass;
    }

    /**
     * Returns slug of whole library
     * @return string
     */
    public function getLibrarySlug()
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
            if (strpos(__DIR__, get_stylesheet_directory()) !== false) {
                $assets = str_replace(
                    get_stylesheet_directory(),
                    get_stylesheet_directory_uri(),
                    __DIR__
                );
                $this->assetsUri = $assets.'/assets/';
                $this->assetsUri = strpos($this->assetsUri, 'http') !== 0 ? get_home_url().$this->assetsUri : $this->assetsUri;
            } else {
                $this->assetsUri = plugins_url('', __FILE__).'/assets/';
            }
        }
        return $this->assetsUri;
    }

    /**
     * Get library assets uri
     * @since  2.26.4
     * @return string
     */
    protected function getLibraryAssetsUri()
    {
        if (empty($this->libraryAssetsUri)) {
            $this->libraryAssetsUri = plugins_url('', dirname(dirname(dirname(__FILE__)))).'/assets/';
        }

        return $this->libraryAssetsUri;
    }

    /**
     * Set field template directory
     * @since  2.24.4
     * @param  string $templateDirectory
     * @return void
     */
    public function setTemplateDirectory($templateDirectory)
    {
        $this->templateDirectory = $templateDirectory;
    }

    /**
     * Get field template
     * @since  2.24.4
     * @return \kabar\Utility\Template\Template
     */
    protected function getTemplate()
    {
        if (empty($this->templateDirectory)) {
            $templateDirectoryPath = __DIR__.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'Default'.DIRECTORY_SEPARATOR;
        } else {
            $templateDirectoryPath = __DIR__.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.$this->templateDirectory.DIRECTORY_SEPARATOR;
        }
        $templatePath = $templateDirectoryPath.$this->getFieldType().'.php';
        $template     = new \kabar\Utility\Template\Template;
        $template($templatePath);
        return $template;
    }

    /**
     * Returns module name
     * @return string
     */
    private function getFieldType()
    {
        if (!$this->fieldType) {
            $this->fieldType = substr(strrchr(get_class($this), "\\"), 1);
        }
        return $this->fieldType;
    }
}
