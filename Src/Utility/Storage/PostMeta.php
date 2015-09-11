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
     * Id
     * @since 2.27.7
     * @var integer
     */
    private $id;

    /**
     * Prefix for keys
     * @var string
     */
    private $prefix;

    // INTERFACE

    /**
     * Set ID just in case storage object cannot determine it automaticaly
     * @since 2.27.7
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = (integer) $id;
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
    public function getPrefixedKey($key)
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
        return isset($_POST[$this->getPrefixedKey($key)]) ? $_POST[$this->getPrefixedKey($key)] : null;
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
            'meta_key'   => $this->getStorageId($key),
            'meta_value' => $value,
            'fields'     => 'ID'
        );

        $userQuery = new \WP_Query($args);

        return $userQuery->get_results();
    }

    // INTERNAL

    /**
     * Returns storage id
     * @since  2.25.1
     * @param  string $key
     * @return string
     */
    private function getStorageId($key)
    {
        return self::HIDE_CUSTOM_FIELD.$this->prefix.$key;
    }

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
        if (!$post instanceof \WP_Post) {
            trigger_error('Cannot determine post ID.', E_USER_ERROR);
        }
        return (integer) $post->ID;
    }
}
