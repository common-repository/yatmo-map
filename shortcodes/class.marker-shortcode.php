<?php
/**
 * Marker Shortcode
 *
 * Use with [yatmo-marker ...]
 * 
 * @category Shortcode
 * @author   Yatmo SRL
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once yatmoMapPluginDir . 'shortcodes/class.shortcode.php';

/**
 * Yatmo Marker Shortcode Class
 */
class Yatmo_Marker_Shortcode extends Yatmo_MapPlugin_Shortcode
{
    /**
     * Get Script for Shortcode
     * 
     * @param string $atts    could be an array
     * @param string $content optional
     * 
     * @return null
     */
    protected function yatmogetHTML($atts='', $content=null)
    {
        if (!empty($atts)) {
            extract($atts, EXTR_SKIP);
        }

        if (!empty($address)) {
            include_once yatmoMapPluginDir . 'class.geocoder.php';
            $location = new Yatmo_Geocoder( $address );
            $lat = $location->lat;
            $lng = $location->lng;
        }

        $lat_set = isset($lat) || isset($y);
        $lng_set = isset($lng) || isset($x);
		
        $lat = empty($lat) ? ( empty($y) ? '0' : $y ) : $lat;
        $lng = empty($lng) ? ( empty($x) ? '0' : $x ) : $lng;

        // validate lat/lng
        $lat = $this->LM->filter_float($lat);
        $lng = $this->LM->filter_float($lng);
		
		if (intval($anonymous) == 1){
			$anonymous = 1;
			wp_enqueue_script('turf_js');
			
			if (rand(0,1) == 1){
				$lat += (0.0001+lcg_value()*(abs(0.004-0.0001)));
			}
			else{
				$lat -= (0.0001+lcg_value()*(abs(0.004-0.0001)));
			}
			
			if (rand(0,1) == 1){
				$lng += (0.0001+lcg_value()*(abs(0.004-0.0001)));
			}
			else{
				$lng -= (0.0001+lcg_value()*(abs(0.004-0.0001)));
			}
		}

        $options = array(
            'lat' => isset($lat) ? esc_html($lat) : null,
			'lng' => isset($lng) ? esc_html($lng) : null,
            'title' => isset($title) ? esc_html($title) : null,
            'popuptextcontent' => isset($popuptextcontent) ? esc_html($popuptextcontent) : null,
            'iconurl' => isset($iconurl) ? esc_html($iconurl) : null,
            'iconwidth' => isset($iconwidth) ? esc_html($iconwidth) : null,
            'iconheight' => isset($iconheight) ? esc_html($iconheight) : null,
            'anonymous' => isset($anonymous) ? esc_html($anonymous) : null,
            'color' => isset($color) ? esc_html($color) : null
        );

        $args = array(
			'lat' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
			'lng' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'title' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'popuptextcontent' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'iconurl' => FILTER_SANITIZE_URL,
            'iconwidth' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'iconheight' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'anonymous' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'color' => FILTER_SANITIZE_FULL_SPECIAL_CHARS
        );
		
		$options = $this->LM->json_sanitize($options, $args);

        start_output_buffer();
        
        ?>
var marker = <?php echo _e($options); ?>;

window.WPYatmoMapPlugin.markers.push( marker );
        <?php
        
        $script = ob_get_clean();

        return $this->wrap_script($script, 'WPYatmoMarkerShortcode');
    }
}