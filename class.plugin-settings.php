<?php
/**
 * Class for getting and setting db/default values
 * 
 * @category Admin
 * @author   Yatmo SRL
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once yatmoMapPluginDir . 'class.plugin-option.php';

// TODO: add option to reset just a single field

/**
 * Used to get and set values
 * 
 * Features:
 * * Add prefixes to db options
 * * built-in admin settings page method
 */
class yatmoMapPluginSettings
{
    /**
     * Prefix for options, for unique db entries
     * 
     * @var string $prefix
     */
    public $prefix = 'yatmo_';
    
    /**
     * Singleton instance
     * 
     * @var yatmoMapPluginSettings
     **/
    private static $_instance = null;

    /**
     * Default values and admin form information
     * Needs to be created within __construct
     * in order to use a function such as __()
     * 
     * @var array $options
     */
    public $options = array();

    /**
     * Singleton
     * 
     * @static
     * 
     * @return yatmoMapPluginSettings
     */
    public static function init() {
        if ( !self::$_instance ) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Instantiate the class
     */
    private function __construct() 
    {

        /* update yatmo version from main class */
        $yatmo_version = yatmo_map::$yatmo_version;

        $foreachmap = esc_html('You can also change this for each map', 'yatmo-map');

        /* 
        * initiate options using internationalization! 
        */
        $this->options = array(
            'default_lat' => array(
                'display_name'=>esc_html('Default latitude', 'yatmo-map'),
                'default'=>'44.67',
                'type' => 'number',
                'helptext' => sprintf(
                    '%1$s %2$s <br /> <code>[yatmo-map lat="44.67"]</code>', 
                    esc_html('Default latitude for maps (please use dot separator!).', 'yatmo-map'),
                    $foreachmap
                )
            ),
            'default_lng' => array(
                'display_name'=>esc_html('Default longitude', 'yatmo-map'),
                'default'=>'-63.61',
                'type' => 'number',
                'helptext' => sprintf(
                    '%1$s %2$s <br /> <code>[yatmo-map lng="-63.61"]</code>', 
                    esc_html('Default longitude for maps (please use dot separator!).', 'yatmo-map'),
                    $foreachmap
                )
            ),
            'default_zoom' => array(
                'display_name'=>esc_html('Default zoom', 'yatmo-map'),
                'default'=>'15',
                'type' => 'number',
				'min' => 0,
				'max' => 20,
				'step' => 1,
                'helptext' => sprintf(
                    '%1$s %2$s <br /> <code>[yatmo-map zoom="5"]</code>', 
                    esc_html('Default zoom for maps.', 'yatmo-map'),
                    $foreachmap
                )
            ),
            'default_height' => array(
                'display_name'=>esc_html('Default height', 'yatmo-map'),
                'default'=>'250',
                'type' => 'text',
                'helptext' => sprintf(
                    '%1$s %2$s <br /> <code>[yatmo-map height="250"]</code>', 
                    esc_html('Default height for maps. Values can include "px" but it is not necessary. Can also be "%". ', 'yatmo-map'),
                    $foreachmap
                )
            ),
            'default_width' => array(
                'display_name'=>esc_html('Default width', 'yatmo-map'),
                'default'=>'100%',
                'type' => 'text',
                'helptext' => sprintf(
                    '%1$s %2$s <br /> <code>[yatmo-map width="100%%"]</code>', 
                    esc_html('Default width for maps. Values can include "px" but it is not necessary.  Can also be "%".', 'yatmo-map'),
                    $foreachmap
                )
            ),
            'default_enable_pois' => array(
                'display_name'=>esc_html('Enable points of interest', 'yatmo-map'),
                'default'=>'1',
                'type' => 'checkbox',
                'helptext' => sprintf(
                    '%1$s %2$s <br /> <code>[yatmo-map enable_pois="1"]</code>', 
                    esc_html('Default configuration for maps to enable points of interest.', 'yatmo-map'),
                    $foreachmap
                )
            ),            
            'default_style' => array(
                'display_name'=>esc_html('Default style', 'yatmo-map'),
                'default'=>'1',
                'type' => 'select',
                'options' => array(
                    '1' => 'Style 1',
                    '2' => 'Style 2',
                    '3' => 'Style 3',
                    '4' => 'Style 4',
                    '5' => 'Style 5',
                    '6' => 'Style 6'                    
                ),
                'helptext' => sprintf(
                   '<div class="containerImg"><img src="%1$sincludes/images/style1.png" alt="style 1"><div class="centeredImgText">Style 1</div></div>
					<div class="containerImg containerImgRight"><img src="%1$sincludes/images/style2.png" alt="style 2"><div class="centeredImgText">Style 2</div></div>
					<div class="containerImg"><img src="%1$sincludes/images/style3.png" alt="style 3"><div class="centeredImgText">Style 3</div></div>
					<div class="containerImg containerImgRight"><img src="%1$sincludes/images/style4.png" alt="style 4"><div class="centeredImgText">Style 4</div></div>
					<div class="containerImg"><img src="%1$sincludes/images/style5.png" alt="style 5"><div class="centeredImgText">Style 5</div></div>
					<div class="containerImg containerImgRight"><img src="%1$sincludes/images/style6.png" alt="style 6"><div class="centeredImgText centeredImgTextW">Style 6</div></div>
					<br /> %2$s <br /> <code>[yatmo-map style="1"]</code>',
					plugin_dir_url( __FILE__ ),
                    $foreachmap
                )
            ),
            'default_language' => array(
                'display_name'=>esc_html('Default language', 'yatmo-map'),
                'default'=>'EN',
                'type' => 'select',
                'options' => array(
                    'EN' => 'EN (English)',
                    'FR' => 'FR (Français)',
                    'IT' => 'IT (Italiano)',
                    'ZH' => 'ZH (汉语)',
                    'DE' => 'DE (Deutsch)',
                    'ES' => 'ES (Español)',
                    'HI' => 'HI (हिन्दी)',
                    'JA' => 'JA (日本語)',
                    'PT' => 'PT (Português)',
                    'NL' => 'NL (Nederlands)',
                    'CA' => 'CA (Català)',
                    'RU' => 'RU (Русский)',
                ),
                'helptext' => sprintf(
                    '%1$s %2$s <br /> <code>[yatmo-map language="EN"]</code>', 
                    esc_html('Default language for maps.', 'yatmo-map'),
                    $foreachmap
                )
            ),
			'default_enable_isochrone' => array(
                'display_name'=>esc_html('Enable isochrone', 'yatmo-map'),
                'default'=>'0',
                'type' => 'checkbox',
                'helptext' => sprintf(
                    '%1$s %2$s <br /> <code>[yatmo-map enable_isochrone="1"]</code><br/><img src="%3$sincludes/images/isochrone.png" alt="Isochrone example">', 
                    esc_html('Default configuration for maps to enable isochrone function (departure from the center of the map, think to place a marker at this location). <strong>This feature is only available with a key linked to a Yatmo subscription!</strong>', 'yatmo-map'),
                    $foreachmap,
					plugin_dir_url( __FILE__ )
                )
            ),
			'default_fitbounds' => array(
                'display_name'=>esc_html('Fit bounds', 'yatmo-map'),
                'default'=>'0',
                'type' => 'checkbox',
                'helptext' => sprintf(
                    '%1$s %2$s <br /> <code>[yatmo-map fitbounds="1"]</code>', 
                    esc_html('If enabled, all markers on each map will alter the view of the map; i.e. the map will fit to the bounds of all of the markers on the map.', 'yatmo-map'),
                    $foreachmap
                )
            ),
            'yatmo_key' => array(
                'display_name'=>esc_html('Yatmo key (optional!)', 'yatmo-map'),
                'default' => '',
                'type' => 'text',
                'noreset' => true,
                'helptext' => sprintf(
                    '%1$s <a href="https://yatmo.com/en/account/register" target="_blank"> %2$s</a>, %3$s',
                    esc_html('You don\'t need a key to use our map except if you exceed <strong>500 map loads per day</strong>. If you need a key, you can create an account ', 'yatmo-map'),
                    esc_html('here', 'yatmo-map'),
                    esc_html('then supply the key here.', 'yatmo-map')
                )
            ),
        );

        foreach ($this->options as $name => $details) {
            $this->options[ $name ] = new yatmoMapPluginOption($details);
        }
    }

    /**
     * Wrapper for WordPress get_options (adds prefix to default options)
     *
     * @param string $key                
     * 
     * @return varies
     */
   /* public function get($key) 
    {
        $default = $this->options[ $key ]->default;
        $key = $this->prefix . $key;
        return get_option($key, $default);
    }
*/
    public function get($key)
{
    $default = isset($this->options[$key]) ? $this->options[$key]->default : null;
    $key = $this->prefix . $key;
    return get_option($key, $default);
}

    /**
     * Wrapper for WordPress update_option (adds prefix to default options)
     *
     * @param string $key   Unique db key
     * @param varies $value Value to insert
     * 
     * @return yatmoMapPluginSettings
     */
    public function set ($key, $value) {
        $key = $this->prefix . $key;
        update_option($key, $value);
        return $this;
    }

    /**
     * Wrapper for WordPress delete_option (adds prefix to default options)
     *
     * @param string $key Unique db key
     * 
     * @return boolean
     */
    public function delete($key) 
    {
        $key = $this->prefix . $key;
        return delete_option($key);
    }

    /**
     * Delete all options
     *
     * @return yatmoMapPluginSettings
     */
    public function reset()
    {
        foreach ($this->options as $name => $option) {
            if (
                !property_exists($option, 'noreset') ||
                $option->noreset != true
            ) {
                $this->delete($name);
            }
        }
        return $this;
    }
}
