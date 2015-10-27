<?php
/**
 * Site cache module
 *
 * @package    kabar
 * @subpackage module
 * @since      0.12.0
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE Version 3
 */

namespace kabar\Module\Cache;

/**
 * Site cache class
 */
class Cache extends \kabar\Module
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

    /**
     * Current page url
     * @var string
     */
    private $currentUrl;

    /**
     * Use forwarded host?
     * @var bool
     */
    private $useForwardedHost;

    // INTERFACE

    /**
     * Setup
     */
    public function __construct()
    {
        if (isset($_GET['purge'])) {
            $this->startPurge();
        }
        if (isset($_GET['nuke'])) {
            $this->forcePurge();
        }
    }

    /**
     * Start purging cache
     * @return void
     */
    public function startPurge()
    {
        $this->purge = true;
    }

    /**
     * Remove cached data - all or of prticular type
     * @since  0.12.9
     * @param  string $type
     * @return void
     */
    public function forcePurge($type = '')
    {
        global $wpdb;

        $value = self::WP_TRANSIENT_PREFIX . self::TRANSIENT_PREFIX . $type;

        $wpdb->query(
            $wpdb->prepare(
                "
                    DELETE FROM `{$wpdb->base_prefix}options`
                    WHERE `option_name` LIKE %s
                ",
                array(
                    '%' . $value . '%'
                )
            )
        );
    }

    /**
     * Check if we are allowed to cache this page
     *
     * By default we don't use cache in admin area, in customization preview, and during ajax requests.
     *
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

        return !$this->purge;
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
            return $this->get($id, $type) . '<!-- kabar - cached -->';
        }
        // fetch and cache
        $payload = call_user_func_array($callback, $params);
        if ($payload !== false) {
            $this->set($id, $type, $payload);
            return $payload . '<!-- kabar - just cached -->';
        }
        // callback returned false, which indicates error
        trigger_error('cacheHtml: callback returned false.', E_USER_WARNING);
        return '<!-- kabar - cache error -->';
    }

    /**
     * Caches object returned by callback, and stores it as JSON
     * @param  string      $id
     * @param  string      $type
     * @param  callable    $callback
     * @return object|void
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
     * Cache
     * @param  string $payload
     * @return void
     */
    public function set($id, $type, $payload)
    {
        $hash = $this->hash($id, $type);
        if (strlen($hash) > 45) {
            trigger_error('To long type "' . $type . '" for cache entry', E_USER_ERROR);
        }
        $this->transients[$hash] = $payload;
        set_transient($hash, $payload, self::EXPIRATION);
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
    public function getCurrentUrl($useForwardedHost = false)
    {
        if ($this->currentUrl && $this->useForwardedHost === $useForwardedHost) {
            return $this->currentUrl;
        }
        $this->currentUrl       = $this->getUrlOrigin($_SERVER, $useForwardedHost) . $_SERVER['REQUEST_URI'];
        $this->useForwardedHost = $useForwardedHost;
        return $this->currentUrl;
    }

    // INTERNAL

    /**
     * Get cache id
     * @param  string $id
     * @return string
     */
    private function hash($id, $type)
    {
        return self::TRANSIENT_PREFIX . $type . md5(KABAR_VERSION . $id);
    }

    /**
     * Get full url from server vars
     * @param  array   $server
     * @param  boolean $useForwardedHost
     * @return string
     */
    private function getUrlOrigin($server, $useForwardedHost = false)
    {
        $ssl      = (!empty($server['HTTPS']) && $server['HTTPS'] == 'on') ? true : false;
        $protocol = strtolower($server['SERVER_PROTOCOL']);
        $protocol = substr($protocol, 0, strpos($protocol, '/')) . (($ssl) ? 's' : '');
        $port     = $server['SERVER_PORT'];
        $port     = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
        $host     = ($useForwardedHost && isset($server['HTTP_X_FORWARDED_HOST'])) ? $server['HTTP_X_FORWARDED_HOST'] : (isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] : null);
        $host     = isset($host) ? $host : $server['SERVER_NAME'] . $port;
        return $protocol . '://' . $host;
    }
}
