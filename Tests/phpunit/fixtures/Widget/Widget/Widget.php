<?php
/**
 * Test widget
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.50.0
 * @package    kabar
 * @subpackage fixtures
 */

namespace fixtures\Widget\Widget;

class Widget extends \kabar\Widget
{
    /**
     * Widget configuration
     * @return array
     */
    public function config()
    {
        return array(
            'id'          => $this->getLibrarySlug().'_test_widget',
            'title'       => 'Test widget',
            'description' => 'Test widget',
            'css_classes' => 'page-section test-section',
            'template'    => $this->getTemplatesDirectory().'Test.php',
        );
    }
}
