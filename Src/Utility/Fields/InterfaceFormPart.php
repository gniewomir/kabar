<?php

namespace kabar\Utility\Fields;

/**
 * Interface for form parts that are not fields per se
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Fields
 */
interface InterfaceFormPart
{
    /**
     * Render form part
     * @return \kabar\Component\Template\Template
     */
    public function render();

    /**
     * Get slug
     * @return string
     */
    public function getSlug();

    /**
     * Set field template
     * @since  2.20.0
     * @param  string $templateDirectory
     * @return void
     */
    public function setTemplateDirectory($templateDirectory);
}
