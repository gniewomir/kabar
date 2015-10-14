<?php
/**
 * Svg module
 *
 * @author     Gniewomir Świechowski <gniewomir.swiechowski@gmail.com>
 * @since      0.36.0
 * @package    kabar
 * @subpackage Modules
 */

namespace kabar\Module\Svg;

use \kabar\ServiceLocator as ServiceLocator;
use \enshrined\svgSanitize\Sanitizer as Sanitizer;

/**
 * Svg module main class
 */
class Svg extends \kabar\Module\Module\Module implements \enshrined\svgSanitize\data\AttributeInterface, \enshrined\svgSanitize\data\TagInterface
{
    const SVG      = 'svg';
    const SVG_MIME = 'image/svg+xml';

    // INTERFACE

    /**
     * Setup module
     */
    public function __construct()
    {
        add_filter('upload_mimes', array($this, 'allowSvgUpload'));
        add_filter('wp_handle_upload_prefilter', array($this, 'sanitizeSvg'));
    }

    /**
     * Return svg mime type
     * @return string
     */
    public function getMimeType()
    {
        return self::SVG_MIME;
    }

    // INTERNAL

    /**
     * WordPress filter 'upload_mimes'.
     * @internal
     * @param  array $mimes
     * @return array
     */
    public function allowSvgUpload($mimes)
    {
        $mimes[self::SVG] = self::SVG_MIME;
        return $mimes;
    }

    /**
     * WordPress filter 'wp_handle_upload_prefilter'. Sanitize Svg before handling upload
     * @param  array $file
     * @return array
     */
    public function sanitizeSvg($file)
    {
        // bail if uploaded file is not svg
        if ($file['type'] != self::SVG_MIME) {
            return $file;
        }

        // Create a new sanitizer instance
        $sanitizer = new Sanitizer();

        // Set allowed tags & attributes
        $sanitizer->setAllowedTags($this);
        $sanitizer->setAllowedAttrs($this);

        // Load the dirty svg
        $dirtySVG = file_get_contents($file['tmp_name']);

        // Pass it to the sanitizer and get it back clean
        $cleanSVG = $sanitizer->sanitize($dirtySVG);

        // Save sanitized
        $saved = file_put_contents($file['tmp_name'], $cleanSVG);

        if (!$saved) {
            $file['error'] = 'Sanitized svg file could not be saved.';
        }

        return $file;
    }

    /**
     * Returns an array of tags
     *
     * @return array
     */
    public static function getTags()
    {
        return array (
            // HTML
            'a','abbr','acronym','address','area','article','aside','audio','b',
            'bdi','bdo','big','blink','blockquote','body','br','button','canvas',
            'caption','center','cite','code','col','colgroup','content','data',
            'datalist','dd','decorator','del','details','dfn','dir','div','dl','dt',
            'element','em','fieldset','figcaption','figure','font','footer','form',
            'h1','h2','h3','h4','h5','h6','head','header','hgroup','hr','html','i',
            'img','input','ins','kbd','label','legend','li','main','map','mark',
            'marquee','menu','menuitem','meter','nav','nobr','ol','optgroup',
            'option','output','p','pre','progress','q','rp','rt','ruby','s','samp',
            'section','select','shadow','small','source','spacer','span','strike',
            'strong','style','sub','summary','sup','table','tbody','td','template',
            'textarea','tfoot','th','thead','time','tr','track','tt','u','ul','var',
            'video','wbr',

            // SVG
            'svg','altglyph','altglyphdef','altglyphitem','animatecolor',
            'animatemotion','animatetransform','circle','clippath','defs','desc',
            'ellipse','font','g','glyph','glyphref','hkern','image','line',
            'lineargradient','marker','mask','metadata','mpath','path','pattern',
            'polygon','polyline','radialgradient','rect','stop','switch','symbol',
            'text','textpath','title','tref','tspan','view','vkern',

            //MathML
            'math','menclose','merror','mfenced','mfrac','mglyph','mi','mlabeledtr',
            'mmuliscripts','mn','mo','mover','mpadded','mphantom','mroot','mrow',
            'ms','mpspace','msqrt','mystyle','msub','msup','msubsup','mtable','mtd',
            'mtext','mtr','munder','munderover',

            //Text
            '#text'
        );
    }

    /**
     * Returns an array of attributes
     *
     * @return array
     */
    public static function getAttributes()
    {
        return array(
            // HTML
            'accept','action','align','alt','autocomplete','background','bgcolor',
            'border','cellpadding','cellspacing','checked','cite','class','clear','color',
            'cols','colspan','coords','datetime','default','dir','disabled',
            'download','enctype','face','for','headers','height','hidden','high','href',
            'hreflang','id','ismap','label','lang','list','loop', 'low','max',
            'maxlength','media','method','min','multiple','name','noshade','novalidate',
            'nowrap','open','optimum','pattern','placeholder','poster','preload','pubdate',
            'radiogroup','readonly','rel','required','rev','reversed','rows',
            'rowspan','spellcheck','scope','selected','shape','size','span',
            'srclang','start','src','step','style','summary','tabindex','title',
            'type','usemap','valign','value','width','xmlns',

            // SVG
            'accent-height','accumulate','additivive','alignment-baseline',
            'ascent','azimuth','baseline-shift','bias','clip','clip-path',
            'clip-rule','color','color-interpolation','color-interpolation-filters',
            'color-profile','color-rendering','cx','cy','d','dy','dy','direction',
            'display','divisor','dur','elevation','end','fill','fill-opacity',
            'fill-rule','filter','flood-color','flood-opacity','font-family',
            'font-size','font-size-adjust','font-stretch','font-style','font-variant',
            'font-weight','image-rendering','in','in2','k1','k2','k3','k4','kerning',
            'letter-spacing','lighting-color','local','marker-end','marker-mid',
            'marker-start','max','mask','mode','min','offset','operator','opacity',
            'order','overflow','paint-order','path','points','r','rx','ry','radius',
            'restart','scale','seed','shape-rendering','stop-color','stop-opacity',
            'stroke-dasharray','stroke-dashoffset','stroke-linecap','stroke-linejoin',
            'stroke-miterlimit','stroke-opacity','stroke','stroke-width','transform',
            'text-anchor','text-decoration','text-rendering','u1','u2','viewbox',
            'visibility','word-spacing','wrap','writing-mode','x','x1','x2','y',
            'y1','y2','z',

            // MathML
            'accent','accentunder','bevelled','close','columnsalign','columnlines',
            'columnspan','denomalign','depth','display','displaystyle','fence',
            'frame','largeop','length','linethickness','lspace','lquote',
            'mathbackground','mathcolor','mathsize','mathvariant','maxsize',
            'minsize','movablelimits','notation','numalign','open','rowalign',
            'rowlines','rowspacing','rowspan','rspace','rquote','scriptlevel',
            'scriptminsize','scriptsizemultiplier','selection','separator',
            'separators','stretchy','subscriptshift','supscriptshift','symmetric',
            'voffset',

            // XML
            'xlink:href','xml:id','xlink:title','xml:space',

            // Kabar
            'viewBox', 'preserveAspectRatio'
        );
    }
}
