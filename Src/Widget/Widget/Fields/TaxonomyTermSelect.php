<?php
/**
 * Select widget field.
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.0.0
 * @package    kabar
 * @subpackage WidgetFields
 */

namespace kabar\Widget\Widget\Fields;

/**
 * Field class
 */
class TaxonomyTermSelect extends AbstractField
{

    /**
     * Field id
     * @var string
     */
    protected $id;

    /**
     * Field label
     * @var string
     */
    protected $label;

    /**
     * Taxonomy to fetch terms from
     * @since 0.0.0
     * @var array
     */
    protected $taxonomy;

    /**
     * Field default value
     * @var string
     */
    protected $default;

    /**
     * Setup text field object
     * @param string    $id
     * @param string    $label
     * @param mixed     $taxonomy
     * @param string    $default
     */
    public function __construct($id, $label, $taxonomy, $default = null)
    {
        $this->id       = $id;
        $this->label    = $label;
        $this->taxonomy = is_array($taxonomy) ? $taxonomy : array($taxonomy);
        $this->default  = $default;
    }

    /**
     * Returns taxonomy terms as options array
     * @since  0.0.0
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
     * Returns widget field value
     * @param  array $args     Widget arguments.
     * @param  array $instance Saved values from database.
     * @return string
     */
    public function get($args, $instance)
    {
        return isset($instance[$this->id]) ? esc_attr($instance[$this->id]) : $this->default;
    }

    /**
     * Field rendering for back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     * @return kabar\Utility\Template\Template
     */
    public function form($instance)
    {
        $template = parent::form($instance);
        $template->value   = empty($instance[$this->id]) ? $this->default : $instance[$this->id];
        $template->label   = $this->label;
        $template->default = $this->default;
        $template->options = $this->getTerms();
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
    public function update($newInstance, $oldInstance)
    {
        $newInstance[$this->id] = !empty($newInstance[$this->id]) ? esc_attr($newInstance[$this->id]) : '';

        return $newInstance;
    }
}
