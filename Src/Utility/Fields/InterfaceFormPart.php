<?php

namespace kabar\Utility\Fields;

/**
 * Interface for form parts that are not fields per se
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage fields
 */
interface InterfaceFormPart
{
    /**
     * Render form part
     * @return \kabar\Utility\Template\Template
     */
    public function render();

    /**
     * Get slug
     * @return string
     */
    public function getSlug();

    /**
     * Set field template
     * @since  0.24.4
     * @param  string $templateDirectory
     * @return void
     */
    public function setTemplateDirectory($templateDirectory);
}
