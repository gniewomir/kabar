<?php
/**
 * Post meta storage
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage FormFieldsStorage
 */

namespace kabar\Utility\Storage;

/**
 * Class for storig data in post meta
 */
class PostMeta implements InterfaceStorage
{
    /**
     * @see https://codex.wordpress.org/Function_Reference/get_post_meta
     */
    const SINGLE = true;

    /**
     * @see https://codex.wordpress.org/Function_Reference/get_post_meta
     */
    const HIDE_CUSTOM_FIELD = '_';

    /**
     * Prefix for keys
     * @var string
     */
    protected $prefix;

    /**
     * Setup storage
     * @since 2.15.0
     * @param string $prefix Prefix
     */
    public function __construct($prefix = '')
    {
        $this->prefix = $prefix;
    }

    /**
     * Sets prefix used for storing values
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * Returns prefixed key
     * @param string $key
     * @return string
     */
    public function getFieldId($key)
    {
        return $this->prefix.$key;
    }

    /**
     * Returns updated value
     * @param  string $key
     * @return mixed
     */
    public function updated($key)
    {
        return isset($_POST[$this->getFieldId($key)]) ? $_POST[$this->getFieldId($key)] : null;
    }

    /**
     * Save value to storage
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function store($key, $value)
    {
        if (!isset($_REQUEST['post_ID'])) {
            trigger_error('Cannot determine post ID. You cannot store post meta outside save request from edition screen.', E_USER_ERROR);
        }
        update_post_meta(intval($_REQUEST['post_ID']), $this->getPostMetaId($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
     * @see    https://codex.wordpress.org/Function_Reference/get_post_meta
    */
    public function retrieve($key)
    {
        global $post;
        if (!$post instanceof \WP_Post) {
            trigger_error('Cannot determine post ID. You cannot retrieve post meta outside WordPress loop.', E_USER_ERROR);
        }
        return get_post_meta($post->ID, $this->getPostMetaId($key), self::SINGLE);
    }

    /**
     * Returns post meta name
     * @since  2.24.4
     * @param string $key
     * @return string
     */
    private function getPostMetaId($key)
    {
        return self::HIDE_CUSTOM_FIELD.$this->prefix.$key;
    }
}
