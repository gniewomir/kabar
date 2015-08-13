<?php
/**
 * Post meta storage
 *
 * WRANING: As utility class it don't perform any security checks. It is responsibility of parent module,
 * to check nonce, user premissions etc.
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
        $key = $this->prefix.$key;
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        return null;
    }

    /**
     * Save value to storage
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function store($key, $value)
    {
        if (isset($_REQUEST['post_ID'])) {
            // this is a save request from edition screen
            $postId = intval($_REQUEST['post_ID']);
        }

        if (empty($postId)) {
            trigger_error('Cannot determine post ID. You cannot store post meta outside save request from edition screen.', E_USER_WARNING);
            return;
        }

        $key = self::HIDE_CUSTOM_FIELD.$this->prefix.$key;
        update_post_meta($postId, $key, $value);
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
        if (empty($post) || empty($post->ID)) {
            trigger_error('Cannot determine post ID. You cannot retrieve post meta outside WordPress loop.', E_USER_WARNING);
            return;
        }

        $key = self::HIDE_CUSTOM_FIELD.$this->prefix.$key;
        return get_post_meta($post->ID, $key, self::SINGLE);
    }
}
