<?php
/**
 * Term meta storage
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.33.0
 * @package    kabar
 * @subpackage FormFieldsStorage
 */

namespace kabar\Utility\Storage;

/**
 * Class for storing data in term meta
 */
final class TermMeta implements InterfaceStorage
{
    /**
     * @see https://codex.wordpress.org/Function_Reference/get_metadata
     */
    const SINGLE = true;

    /**
     * Id
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
     * Setup storage object
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
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Returns prefixed field key
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
        // available since WP 4.4 or implemented by plugin
        update_term_meta($this->getTermId(), $this->getPrefixedKey($key), $value);
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
    */
    public function retrieve($key)
    {
        // available since WP 4.4 or implemented by plugin
        return get_term_meta($this->getTermId(), $this->getPrefixedKey($key), self::SINGLE);
    }

    /**
     * Search for key/value pair and return array of id's
     * @param  string  $key
     * @param  mixed   $value
     * @return integer
     */
    public function search($key, $value)
    {
        $args = array(
            'meta_key'   => $this->getPrefixedKey($key),
            'meta_value' => $value,
            'fields'     => 'ID'
        );

        $userQuery = new \WP_User_Query($args);

        return $userQuery->get_results();
    }

    // INTERNAL

    /**
     * Get term id
     * @return integer
     */
    private function getTermId()
    {
        if ($this->id) {
            return $this->id;
        }
        if (defined(WP_DEBUG) && WP_DEBUG) {
            trigger_error('Cannot determine term ID.', E_USER_WARNING);
        }
        return false;
    }
}
