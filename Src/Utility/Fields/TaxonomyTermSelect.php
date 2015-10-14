<?php
/**
 * Taxonomy term select field
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage fields
 */

namespace kabar\Utility\Fields;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Handles redering select field, keeping text field save/get methods
 */
class TaxonomyTermSelect extends Select
{

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
     * Taxonomy to fetch fields from
     * @var array
     */
    protected $taxonomy;

    /**
     * Array of terms in pairs label => slug
     * @var array
     */
    protected $terms;

    /**
     * Field default value
     * @var mixed
     */
    protected $default;

    /**
     * Field template file path
     * @var string
     */
    protected $template;

    /**
     * Setup field
     *
     * Passing null as default value will add empty option to select field which will be selected by default
     *
     * @param string $slug
     * @param string $title
     * @param mixed  $taxonomy WordPress taxonomy to fetch terms from
     * @param mixed  $default
     */
    public function __construct($slug, $title, $taxonomy, $default = null)
    {
        $this->slug     = $slug;
        $this->title    = $title;
        $this->taxonomy = is_array($taxonomy) ? $taxonomy : array($taxonomy);
        $this->default  = $default;
    }

    /**
     * Returns taxonomy terms as options array
     * @return array
     */
    protected function getTerms()
    {
        $args = array(
            'orderby'           => 'name',
            'order'             => 'ASC',
            'hide_empty'        => true,
            'exclude'           => array(),
            'exclude_tree'      => array(),
            'include'           => array(),
            'number'            => '',
            'fields'            => 'all',
            'slug'              => '',
            'parent'            => '',
            'hierarchical'      => true,
            'child_of'          => 0,
            'childless'         => false,
            'get'               => '',
            'name__like'        => '',
            'description__like' => '',
            'pad_counts'        => true,
            'offset'            => '',
            'search'            => '',
            'cache_domain'      => 'core'
        );
        $terms = get_terms($this->taxonomy, $args);

        if (!is_array($terms)) {
            trigger_error('Couldn\'t retrieve taxonomy terms.', E_USER_WARNING);
            return array();
        }

        $options = array();
        foreach ($terms as $term) {
            $options[$term->name] = $term->slug;
        }
        return $options;
    }

    /**
     * Render field
     * @return \kabar\Utility\Template\Template
     */
    public function render()
    {
        $template           = $this->getTemplate();
        $template->id       = $this->storage->getPrefixedKey($this->getSlug());
        $template->cssClass = $this->getCssClass();
        $template->title    = $this->title;
        $template->options  = $this->getTerms();
        $template->default  = $this->default;
        $template->value    = empty($this->get()) ? $this->default : $this->get();
        return $template;
    }
}
