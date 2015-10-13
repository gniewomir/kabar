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
 * Class for storing data in post meta
 */
final class PostMeta extends HTTPPost implements InterfaceStorage
{
    /**
     * @see https://codex.wordpress.org/Function_Reference/get_post_meta
     */
    const SINGLE = true;

    /**
     * @see https://codex.wordpress.org/Function_Reference/get_post_meta
     */
    const HIDE_CUSTOM_FIELD = '_';

    // INTERFACE

    /**
     * Setup storage object
     * @since 2.31.0
     * @param string       $prefix
     * @param integer|null $id
     */
    public function __construct($prefix = '', $id = null)
    {
        $this->prefix = $prefix;
        $this->id     = $id;
    }

    /**
     * Set ID just in case storage object cannot determine it automaticaly
     * @since 2.27.7
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Returns storage key
     * @since  2.50.0
     * @param  string $key
     * @return string
     */
    public function getStorageKey($key)
    {
        return self::HIDE_CUSTOM_FIELD.$this->getPrefixedKey($key);
    }

    /**
     * Save value to storage
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function store($key, $value)
    {
        update_metadata('post', $this->getPostId(), $this->getStorageKey($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
     * @see    https://codex.wordpress.org/Function_Reference/get_post_meta
    */
    public function retrieve($key)
    {
        return get_metadata('post', $this->getPostId(), $this->getStorageKey($key), self::SINGLE);
    }

    /**
     * Search for key/value pair and return array of id's
     * @since  2.27.7
     * @param  string  $key
     * @param  mixed   $value
     * @return integer
     */
    public function search($key, $value)
    {
        $args = array(
            'meta_key'   => $this->getStorageKey($key),
            'meta_value' => $value,
            'fields'     => 'ID'
        );

        $userQuery = new \WP_Query($args);

        return $userQuery->get_results();
    }

    // INTERNAL

    /**
     * Get post id
     * @since  2.25.1
     * @return integer
     */
    private function getPostId()
    {
        if ($this->id) {
            return $this->id;
        }
        global $post;
        if ($post instanceof \WP_Post) {
            return $post->ID;
        }
        trigger_error('Cannot determine post ID.', E_USER_ERROR);
    }
}
