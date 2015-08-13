<?php
/**
 * HTTPPost storage
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
class HTTPPost implements InterfaceStorage
{

    /**
     * Prefix for keys
     * @var string
     */
    protected $prefix = '';

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
        $key = $this->prefix.$key;
        $_POST[$key] = $value;
    }

    /**
     * Retrieve stored value
     * @param  string $key
     * @return mixed
    */
    public function retrieve($key)
    {
        $key = $this->prefix.$key;
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }
}
