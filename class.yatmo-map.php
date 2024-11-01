<?php
/**
 * Yatmo Map Class File
 * 
 * @category Admin
 * @author   Yatmo SRL
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Yatmo Map Class
 */
class yatmo_map
{

    /**
     * Yatmo version
     * 
     * @var string major minor patch version
     */
    public static $yatmo_version = '1.0.0';

    /**
     * Files to include upon init
     * 
     * @var array $_shortcodes
     */
    private $_shortcodes = array(
        'yatmo-map' => array(
            'file' => 'class.map-shortcode.php',
            'class' => 'yatmoMapShortcode'
        ),
		'yatmo-marker' => array(
            'file' => 'class.marker-shortcode.php',
            'class' => 'Yatmo_Marker_Shortcode'
        )
    );

    /**
     * Singleton Instance of Yatmo Map
     * 
     * @var yatmo_map
     **/
    private static $instance = null;

    /**
     * Singleton init Function
     * 
     * @static
     */
    public static function init() {
        if ( !self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * yatmo_map Constructor
     */
    private function __construct() {
        $this->init_hooks();
        $this->add_shortcodes();

        // loaded
        do_action('yatmo_map_loaded');
    }

    /**
     * Add actions and filters
     */
    private function init_hooks()
    {

        // yatmoMapPluginSettings
        include_once yatmoMapPluginDir . 'class.plugin-settings.php';

        // yatmo_map_Admin
        include_once yatmoMapPluginDir . 'class.admin.php';
        
        // init admin
        yatmo_map_Admin::init();

        add_action( 'plugins_loaded', array('yatmo_map', 'load_text_domain' ));
        
        add_action( 'wp_enqueue_scripts', array('yatmo_map', 'EnqueueAndRegister') );

        $settings = self::settings();

        if ($settings->get('shortcode_in_excerpt')) {
            // allows maps in excerpts
            add_filter('the_excerpt', 'do_shortcode');
        }
    }

    /**
     * Includes and adds shortcodes
     */
    private function add_shortcodes()
    {
        // shortcodes
        $shortcode_dir = yatmoMapPluginDir . 'shortcodes/';
        
        foreach ($this->_shortcodes as $shortcode => $details) {
            require_once $shortcode_dir . $details['file'];
            add_shortcode($shortcode, array($details['class'], 'shortcode'));
        }
    }

    /**
     * Triggered when user uninstalls/removes plugin
     */
    public static function uninstall()
    {
        // remove settings in db
        // it needs to be included again because __construct 
        // won't need to execute
        $settings = self::settings();
        $settings->reset();

        // remove geocoder locations in db
        include_once yatmoMapPluginDir . 'class.geocoder.php';
        Yatmo_Geocoder::yatmoRemoveCaches();
    }

    /**
     * Loads Translations
     */
    public static function load_text_domain()
    {
        load_plugin_textdomain( 'yatmo-map', false, dirname( plugin_basename( yatmoMapPluginFile ) ) . '/languages/' );
    }

    /**
     * Enqueue and register styles and scripts (called in __construct)
     */
    public static function EnqueueAndRegister()
    {
        /* defaults from db */
        $settings = self::settings();

        $js_url = $settings->get('js_url');
        wp_register_script('yatmo_js', 'https://map.yatmo.com/map.js', Array(), null, true);
        
        /* run a construct function in the document head for subsequent functions to use (it is lightweight) */
        $minified = true; // Set a default value for the $minified variable

// Your code...

wp_register_script('wp_yatmo_map', plugins_url(sprintf('scripts/construct-yatmo-map%s.js', $minified ? '.min' : ''), __FILE__), Array('yatmo_js'), yatmo_map__PLUGIN_VERSION, false);
wp_register_script('turf_js', plugins_url('scripts/turf.min.js', __FILE__), Array('yatmo_js'), yatmo_map__PLUGIN_VERSION, false);

    }

    /**
     * Filter for removing nulls from array
     *
     * @param array $arr
     * 
     * @return array with nulls removed
     */
    public function filter_null($arr)
    {
        if (!function_exists('remove_null')) {
            function remove_null ($var) {
                return $var !== null;
            }
        }

        return array_filter($arr, 'remove_null');
    }

    /**
     * Filter for removing empty strings from array
     *
     * @param array $arr
     * 
     * @return array with empty strings removed
     */
    public function filter_empty_string($arr)
    {
        if (!function_exists('remove_empty_string')) {
            function remove_empty_string ($var) {
                return $var !== "";
            }
        }

        return array_filter($arr, 'remove_empty_string');
    }

    /**
     * Sanitize any given validations, but concatenate with the remaining keys from $arr
     */
    public function sanitize_inclusive($arr, $validations) {
        return array_merge(
            $arr,
            $this->sanitize_exclusive($arr, $validations)
        );
    }

    /**
     * Sanitize and return ONLY given validations
     */
    public function sanitize_exclusive($arr, $validations) {
        // remove nulls
        $arr = $this->filter_null($arr);

        // sanitize output
        $args = array_intersect_key($validations, $arr);
        return filter_var_array($arr, $args);
    }

    /**
     * Sanitize JSON 
     *
     * Takes options for filtering/correcting inputs for use in JavaScript
     *
     * @param array $arr     user-input array
     * @param array $args    array with key-value definitions on how to convert values
     * @return array corrected for JavaScript
     */
    public function json_sanitize($arr, $args)
    {
        $arr = $this->sanitize_exclusive($arr, $args);

        $output = json_encode($arr);

        // always return object; not array
        if ($output === '[]') {
            $output = '{}';
        }

        return $output;
    }

    /**
     * Get settings from yatmoMapPluginSettings
     * @return yatmoMapPluginSettings
     */
    public static function settings () {
        include_once yatmoMapPluginDir . 'class.plugin-settings.php';
        return yatmoMapPluginSettings::init();
    }

    /**
     * Parses liquid tags from a string
     * 
     * @param string $str
     * 
     * @return array|null
     */
    public function liquid ($str) {
        if (!is_string($str)) {
            return null;
        }
        $templateRegex = "/\{ *(.*?) *\}/";
        preg_match_all($templateRegex, $str, $matches);
               
        if (!$matches[1]) {
            return null;
        }
        
        $str = $matches[1][0];

        $tags = explode(' | ', $str);

        $original = array_shift($tags);

        if (!$tags) {
            return null;
        }

        $output = array();

        foreach ($tags as $tag) {
            $tagParts = explode(': ', $tag);
            $tagName = array_shift($tagParts);
            $tagValue = implode(': ', $tagParts) || true;

            $output[$tagName] = $tagValue;
        }

        // preserve the original
        $output['original'] = $original;

        return $output;
    }

    /**
     * Renders a json-like string, removing quotes for values
     * 
     * allows JavaScript variables to be added directly 
     * 
     * @return string
     */
    public function rawDict ($arr) {
        $obj = '{';
        
        foreach ($arr as $key=>$val) {
            $obj .= "\"$key\": $val,";
        }

        $obj .= '}';

        return $obj;
    }

    /**
     * Filter all floats to remove commas, force decimals, and validate float
     * see: https://wordpress.org/support/topic/all-maps-are-gone/page/3/#post-14625548
     */
    public function filter_float ($flt) {
        // make sure the value actually is a float
        $out = filter_var($flt, FILTER_VALIDATE_FLOAT);
        
        // some locales seem to force commas
        $out = str_replace(',', '.', $out);
        
        return $out;
    }

    /**
     * Bounds are given as "50, -114; 52, -112"
     * Converted to 2d-array: [[50, -114], [52, -112]]
     */
    public function convert_bounds_str_to_arr ($bounds) {
        if (isset($bounds)) {
            try {
                // explode by semi-colons and commas
                $arr = preg_split("[;|,]", $bounds);

                return array(
                    array(
                        $this->filter_float($arr[0]), 
                        $this->filter_float($arr[1])
                    ),
                    array(
                        $this->filter_float($arr[2]), 
                        $this->filter_float($arr[3])
                    )
                );
            } catch (Exception $e) {
                return null;
            }
        }

        return null;
    }
}
