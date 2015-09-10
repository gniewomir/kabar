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
final class PostMeta implements InterfaceStorage
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
    private $prefix;

    // INTERFACE

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
     * Returns storage id
     * @since  2.25.1
     * @param  string $key
     * @return string
     */
    public function getStorageId($key)
    {
        return self::HIDE_CUSTOM_FIELD.$this->prefix.$key;
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
        update_post_meta($this->getPostId(), $this->getStorageId($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
     * @see    https://codex.wordpress.org/Function_Reference/get_post_meta
    */
    public function retrieve($key)
    {
        return get_post_meta($this->getPostId(), $this->getStorageId($key), self::SINGLE);
    }

    // INTERNAL

    /**
     * Get post id
     * @since  2.25.1
     * @return integer
     */
    private function getPostId() {
        global $post;
        if (!$post instanceof \WP_Post) {
            trigger_error('Cannot determine post ID.', E_USER_ERROR);
        }
        return (integer) $post->ID;
    }
}
