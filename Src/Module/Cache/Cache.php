<?php
/**
 * Site cache module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.12.0
 * @package    kabar
 * @subpackage modules
 */

namespace kabar\Module\Cache;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Site cache class
 */
class Cache extends \kabar\Module\Module\Module
{
    /**
     * WordPress transient prefix
     */
    const WP_TRANSIENT_PREFIX = '_transient_';

    /**
     * Prefix for cache transistents
     * md5 hash takes 32 characters, so prefix cannot exceed 13 characters,
     * as we are limited to 45 characters in transient name
     * @see https://codex.wordpress.org/Function_Reference/get_transient
     */
    const TRANSIENT_PREFIX = 'kbc';

    /**
     * We want to make sure that it is always lower than nonce lifetime
     * @see http://myatus.com/p/wordpress-caching-and-nonce-lifespan/
     * @see https://codex.wordpress.org/Function_Reference/set_transient
     */
    const EXPIRATION = 39600; // 60 * 60 * 11 = 11h

    /**
     * If we should start purging cache
     * @var boolean
     */
    private $purge = false;

    /**
     * Transients fetched from database
     * @var array
     */
    private $transients = array();

    // INTERFACE

    /**
     * Setup
     */
    public function __construct()
    {
        $this->requireBeforeAction('after_setup_theme');

        if (isset($_GET['purge'])) {
            $this->startPurge();
        }
        if (isset($_GET['nuke'])) {
            $this->forcePurge();
        }
    }

    /**
     * Start purging cache
     * @return bool
     */
    public function startPurge()
    {
        $this->purge = true;
    }

    /**
     * Remove cached data - all or of prticular type
     * @since  2.12.9
     * @param  string $type
     * @return void
     */
    public function forcePurge($type = '')
    {
        global $wpdb;

        $option = '';
        $value  = self::WP_TRANSIENT_PREFIX.self::TRANSIENT_PREFIX.$type;

        $result = $wpdb->get_results(
            $wpdb->prepare(
                "
                    DELETE FROM `{$wpdb->base_prefix}options`
                    WHERE `option_name` LIKE %s
                ",
                array(
                    '%'.$value.'%'
                )
            )
        );
    }

    /**
     * If we are allowed to cache this page
     * @return boolean
     */
    public function isCacheable()
    {
        if (is_admin()) {
            return false;
        }

        if (is_customize_preview()) {
            return false;
        }

        if (defined('DOING_AJAX') && DOING_AJAX) {
            return false;
        }

        if ($this->purge) {
            return false;
        }

        return true;
    }

    /**
     * If this page is cached
     * @return boolean
     */
    public function isCached($id, $type)
    {
        $cached = $this->get($id, $type);
        return $cached !== false;
    }

    /**
     * Caches html returned by callback or provided as argument
     * @param  string          $id
     * @param  string          $type
     * @param  string|callable $callback
     * @param  array           $params
     * @return string
     */
    public function cacheHtml($id, $type, callable $callback, $params = array())
    {
        // return cached
        if ($this->isCacheable() && $this->isCached($id, $type)) {
            return $this->get($id, $type).'<!-- cached -->';
        }
        // fetch and cache
        $payload = call_user_func_array($callback, $params);
        if ($payload !== false) {
            $this->set($id, $type, $payload);
            return $payload.'<!-- just cached -->';
        }
        // callback returned false, which indicates error
        trigger_error('cacheHtml: callback returned false.', E_USER_WARNING);
        return '<!-- error -->';
    }

    /**
     * Caches object returned by callback, and stores it as JSON
     * @param  string   $id
     * @param  string   $type
     * @param  callable $callback
     * @return object
     */
    public function cacheObjectAsJson($id, $type, callable $callback)
    {
        // return cached
        if ($this->isCacheable() && $this->isCached($id, $type)) {
            $payload = $this->get($id, $type);
            $payload = json_decode($payload);
            // check for empty objects
            if (is_object($payload)) {
                $test = (array) $payload;
                if (!empty($test)) {
                    return $payload;
                } else {
                    trigger_error('Cached empty object. Running callback.', E_USER_WARNING);
                }
            }
        }
        // fetch and cache
        $payload = call_user_func($callback);
        if (is_object($payload)) {
            // @see http://stackoverflow.com/questions/804045/preferred-method-to-store-php-arrays-json-encode-vs-serialize
            // @see https://codex.wordpress.org/Function_Reference/set_transient
            $this->set($id, $type, json_encode($payload, JSON_UNESCAPED_UNICODE));
            return $payload;
        }
        trigger_error('Callback must return object.', E_USER_ERROR);
    }

    /**
     * Cache
     * @param  string $payload
     * @return void
     */
    public function set($id, $type, $payload)
    {
        $hash = $this->hash($id, $type);
        if (strlen($hash) > 45) {
            trigger_error('To long type "'.$type.'"for cache entry', E_USER_ERROR);
        }
        $this->transients[$hash] = $payload;
        set_transient($hash, $payload, self::EXPIRATION);
    }

    /**
     * Get cached data
     * @param  string $id
     * @return string|bool
     */
    public function get($id, $type)
    {
        $hash = $this->hash($id, $type);
        if (!isset($this->transients[$hash])) {
            $this->transients[$hash] = get_transient($hash);
        }
        return $this->transients[$hash];
    }

    /**
     * Delete cache entry
     * @param  string $id
     * @return void
     */
    public function delete($id, $type)
    {
        $hash = $this->hash($id, $type);
        unset($this->transients[$hash]);
        delete_transient($hash);
    }

    /**
     * Get current page url
     * @param  boolean $useForwardedHost
     * @return string
     */
    public function currentUrl($useForwardedHost = false)
    {
        return $this->urlOrigin($_SERVER, $useForwardedHost).$_SERVER['REQUEST_URI'];
    }

    // INTERNAL

    /**
     * Get cache id
     * @param  string $id
     * @return string
     */
    private function hash($id, $type)
    {
        return self::TRANSIENT_PREFIX.$type.md5(ServiceLocator::VERSION.$id);
    }

    /**
     * Get full url from server vars
     * @param  array   $s
     * @param  boolean $useForwardedHost
     * @return string
     */
    private function urlOrigin($s, $useForwardedHost = false)
    {
        $ssl      = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
        $sp       = strtolower($s['SERVER_PROTOCOL']);
        $protocol = substr($sp, 0, strpos($sp, '/')).(($ssl) ? 's' : '');
        $port     = $s['SERVER_PORT'];
        $port     = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':'.$port;
        $host     = ($useForwardedHost && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host     = isset($host) ? $host : $s['SERVER_NAME'].$port;
        return $protocol.'://'.$host;
    }
}
