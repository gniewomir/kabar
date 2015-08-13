<?php
/**
 * Google map
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      2.0.0
 * @package    kabar
 * @subpackage Component
 * @see        https://codex.wordpress.org/Function_Reference/add_meta_box
 */

namespace kabar\Component\GoogleMap;

use \kabar\ServiceLocator as ServiceLocator;

/**
 * Class providing api for displaying Google Maps
 */
class GoogleMap extends \kabar\Module\Module\Module
{

    const IN_FOOTER              = true;
    const GOOGLE_MAPS_API_SCRIPT = 'https://maps.googleapis.com/maps/api/js';

    /**
     * Map id
     * @var string
     */
    protected $id;

    /**
     * Latitude
     * @var numeric
     */
    protected $latitude;

    /**
     * Longitude
     * @var numeric
     */
    protected $longitude;

    /**
     * Zoom
     * @var numeric
     */
    protected $zoom;

    /**
     * Setup map resources
     * @param string $id      Will be used as map ID in DOM
     * @param array  $options Options that should be passed to Google Maps API.
     */
    public function __construct($id, $options = array())
    {
        $this->id      = $id;
        $this->options = $options;

        add_action('wp_enqueue_scripts', array($this, 'addScripts'));
    }

    /**
     * Returns map ID
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add map script
     * @return void
     */
    public function addScripts()
    {
        wp_enqueue_script(
            'google-maps-api',
            self::GOOGLE_MAPS_API_SCRIPT,
            array('jquery'),
            false,
            self::IN_FOOTER
        );
        wp_enqueue_script(
            $this->getLibrarySlug().'-google-maps',
            $this->getAssetsUri().'js/GoogleMap.js',
            array('jquery', 'google-maps-api'),
            $this->getLibraryVersion(),
            self::IN_FOOTER
        );
    }

    /**
     * Parses Google Maps url to get place cooridinates and zoom
     * @param  string $url
     * @return array       False on error
     */
    protected function parseGoogleMapsUrl($url)
    {
        // nothing to process
        if (empty($url)) {
            return false;
        }

        // data in request path
        $path = parse_url($url, PHP_URL_PATH);
        $path = explode('/', $path);
        foreach ($path as $part) {
            if (strpos($part, '@') === 0) {
                $part     = str_replace('@', '', $part);
                $part     = explode(',', $part);
                $coords   = array();
                $coords[] = array_shift($part);
                $coords[] = array_shift($part);
                $zoom     = str_replace('z', '', array_shift($part));
                break;
            }
        }

        // data in request query string
        if (empty($coords[0]) || empty($coords[1]) || empty($zoom)) {
            $queryString = parse_url($url, PHP_URL_QUERY);
            parse_str($queryString, $queryParts);
            if (isset($queryParts['q']) && isset($queryParts['z'])) {
                $coords    = explode(',', $queryParts['q']);
                $zoom      = $queryParts['z'];
            }
        }

        // return data if it looks valid
        if (is_numeric($coords[0]) && is_numeric($coords[1]) && is_numeric($zoom)) {
            return array(
                'latitude'  => array_shift($coords),
                'longitude' => array_shift($coords),
                'zoom'      => $zoom,
            );
        }

        // invalid input
        return false;
    }

    /**
     * Get map html
     * @param mixed  $coords  Array with latitude, longitude and zoom level or Google Maps url
     * @return string
     */
    public function render($coords)
    {

        if (is_array($coords)) {
            $this->latitude  = $coords['latitude'];
            $this->longitude = $coords['longitude'];
            $this->zoom      = $coords['zoom'];
        }

        if (is_string($coords)) {
            $coords = $this->parseGoogleMapsUrl($coords);
            if ($coords === false) {
                $template = ServiceLocator::getNew('Component', 'Template');
                $template->cssClass = $this->getCssClass();
                $template->message = __('Empty or invalid cooridinates. Can\'t show map.', $this->getLibrarySlug());
                $template(__DIR__.'/Templates/Message.php');
                return $template;
            }
            $this->latitude  = $coords['latitude'];
            $this->longitude = $coords['longitude'];
            $this->zoom      = $coords['zoom'];
        }

        $data = array(
            'latitude'    => $this->latitude,
            'longitude'   => $this->longitude,
            'zoom'        => $this->zoom,
            'scrollwheel' => isset($this->options['scrollwheel']) && $this->options['scrollwheel'] ? $this->options['scrollwheel'] : false
        );

        wp_localize_script(
            $this->getLibrarySlug().'-google-maps',
            str_replace('-', '_', $this->getId()),
            $data
        );

        $template = ServiceLocator::getNew('Component', 'Template');
        $template(__DIR__.'/Templates/GoogleMap.php');
        $template->cssClass = $this->getCssClass();
        $template->id       = $this->getId();

        return $template;
    }
}
