<?php
/**
 * Map Shortcode
 *
 * Displays map with [yatmo-map ...atts] 
 * 
 * @author   Yatmo SRL
 * @category Shortcode
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
} 

require_once yatmoMapPluginDir . 'shortcodes/class.shortcode.php';

/**
 * yatmo_Map_Shortcode Class
 */
class yatmoMapShortcode extends Yatmo_MapPlugin_Shortcode
{
    /**
     * Instantiate class
     */
    public function __construct()
    {
        parent::__construct();

        $this->enqueue();
    }

    /**
     * Enqueue Scripts and Styles for Yatmo 
     * 
     * @return null
     */
    protected function enqueue()
    {
        wp_enqueue_style('yatmo_stylesheet');
        wp_enqueue_script('wp_yatmo_map');

        // enqueue user-defined scripts 
        // ! will fire for each map
        do_action('yatmo_map_enqueue');
    }

    /**
     * Merge shortcode options with default options
     *
     * @param array|string $atts    key value pairs from shortcode 
     * 
     * @return array new atts, which is actually an array
     */
    protected function yatmogetAtts($atts='')
    {
        $atts = (array) $atts;
        extract($atts, EXTR_SKIP);

        $settings = yatmoMapPluginSettings::init();

        $atts['language'] = array_key_exists('language', $atts) ? $language : $settings->get('default_language');
        $atts['zoom'] = array_key_exists('zoom', $atts) ? $zoom : $settings->get('default_zoom');
        $atts['enable_isochrone'] = array_key_exists('enable_isochrone', $atts) ? $enable_isochrone : $settings->get('default_enable_isochrone');
        $atts['enable_pois'] = array_key_exists('enable_pois', $atts) ? $enable_pois : $settings->get('default_enable_pois');
        $atts['style'] = array_key_exists('style', $atts) ? $style : $settings->get('default_style');
        $atts['height'] = empty($height) ? $settings->get('default_height') : $height;
        $atts['width'] = empty($width) ? $settings->get('default_width') : $width;
		$atts['fitbounds'] = array_key_exists('fitbounds', $atts) ? 
            $fitbounds : $settings->get('default_fitbounds');

        /* allow percent, but add px for ints */
        $atts['height'] .= is_numeric($atts['height']) ? 'px' : '';
        $atts['width'] .= is_numeric($atts['width']) ? 'px' : '';   

        return $atts;
    }

    /**
     * Get the div tag for the map to instantiate
     * 
     * @param string $height
     * @param string $width
     * 
     * @return string HTML div element
     */
    protected function yatmogetDiv($height, $width) {
        // div does not get wrapped in script tags
		start_output_buffer();
        ?>
<div class="yatmo-map WPYatmoMap" id="yatmap" style="height:<?php 
    echo esc_html($height);
?>; width:<?php 
    echo esc_html($width);
?>; max-width:<?php 
    echo esc_html($width);
?>;"></div><?php
        return ob_get_clean();
    }

    /**
     * Get script for shortcode
     * 
     * @param array  $atts    sometimes this is null
     * @param string $content anything within a shortcode
     * 
     * @return string HTML
     */
    protected function yatmogetHTML($atts='', $content=null)
    {
        extract($this->yatmogetAtts($atts));

        if (!empty($address)) {
            /* try geocoding */
            include_once yatmoMapPluginDir . 'class.geocoder.php';
            $location = new Yatmo_Geocoder($address);
            $lat = $location->lat;
            $lng = $location->lng;
        }

        $settings = yatmoMapPluginSettings::init();

        // map uses lat/lng
        $lat = isset($lat) ? $lat : $settings->get('default_lat');
        $lng = isset($lng) ? $lng : $settings->get('default_lng');
		
        $licenseKey = $settings->get('yatmo_key');
		
		if (empty($licenseKey) || $licenseKey === null) {
            $licenseKey = parse_url(get_site_url(), PHP_URL_HOST);
        }
        
        // validate lat/lng
        $lat = $this->LM->filter_float($lat);
        $lng = $this->LM->filter_float($lng);

        /* should be iterated for multiple maps */
		start_output_buffer();
        ?>/*<script>*/
		
		
		yatmoConfig = {
			licenseKey: '<?php echo esc_html($licenseKey); ?>',
			language: '<?php echo esc_html($language); ?>',
			mapStyle: <?php echo esc_html($style); ?>,
			container: 'yatmap',
			
			<?php if ($enable_pois == 0){
				echo esc_html('noPois: true,');
			} ?>
			
			<?php if ($enable_isochrone == 1){
				echo esc_html('isochrone: { latitude: ' . $lat . ', longitude: ' . $lng . ' },');
			} ?>
			
			center: [<?php echo esc_html($lng); ?>,<?php echo esc_html($lat); ?>],
			zoom: <?php echo esc_html($zoom); ?>
		};
		
		function LoadMap(map){
			map.on('load', function() {
				try {
					var cpt = 1;
					
					<?php if ($fitbounds == 1){
						echo esc_html('var bounds = new mapboxgl.LngLatBounds();');
					} ?>
					
					window.WPYatmoMapPlugin.markers.forEach(markerData => {
						var latToUse, lngToUse;
						var colorToUse = markerData.hasOwnProperty('color') ? markerData.color : '#06A7EA';
						
						if (markerData.hasOwnProperty('lat') && markerData.hasOwnProperty('lng')){
							if (markerData.lng != '0' && markerData.lat != '0'){
								lngToUse = markerData.lng;
								latToUse = markerData.lat;
							}
							else{
								lngToUse = map.getCenter().lng;
								latToUse = map.getCenter().lat;
							}
						}
						else{
							lngToUse = map.getCenter().lng;
							latToUse = map.getCenter().lat;
						}
						
						<?php if ($fitbounds == 1){
							echo esc_html('bounds.extend([lngToUse,latToUse]);');
						} ?>
						
						if (markerData.hasOwnProperty('anonymous') && markerData.anonymous == 1){
							var center = turf.point([lngToUse,latToUse]);
							var radius = 0.75;
							var options = {
								steps: 80,
								units: 'kilometers'
							};

							var circle = turf.circle(center, radius, options);
							
							circle.properties.description = yatmoLabels['<?php echo esc_html($language); ?>'].anonymousPlace;

							map.addLayer({
								"id": "circle-fill" + cpt,
								"type": "fill",
								"source": {
									"type": "geojson",
									"data": circle
								},
								"paint": {
									"fill-color": colorToUse,
									"fill-opacity": 0.4
								}
							});

							map.addLayer({
								"id": "circle-symbol" + cpt,
								"type": "symbol",
								"source": {
									"type": "geojson",
									"data": circle
								},
								'layout': {
									"symbol-placement": "line",
									'text-field': ['get', 'description'],
									'text-variable-anchor': ['center'],
									"text-font": ["Roboto Regular"],
									'text-radial-offset': 0.5,
									'text-justify': 'auto'
								}
							});
							
							map.addLayer({
								"id": "circle-outline" + cpt,
								"type": "line",
								"source": {
									"type": "geojson",
									"data": circle
								},
								"paint": {
									"line-color": colorToUse,
									"line-opacity": 0.5,
									"line-width": 5,
									"line-offset": 2
								}
							});
						
							cpt++;
						}
						
						var marker;
						if (markerData.hasOwnProperty('iconurl')){
							var el = document.createElement('div');
							el.style.backgroundImage ='url(' + markerData.iconurl + ')';
							el.style.width = parseInt(markerData.iconwidth.replace(/\D/g,'')) + 'px';
							el.style.height =  parseInt(markerData.iconheight.replace(/\D/g,'')) + 'px';
							el.style.backgroundSize = '100%';
							marker = new mapboxgl.Marker(el)
						}
						else{
							marker = new mapboxgl.Marker({
								color: colorToUse
							});
						}					
						
						if (markerData.hasOwnProperty('title')){
							marker._element.title = markerData.title;
						}
						
						marker.setLngLat([lngToUse, latToUse]);
						
						if (markerData.hasOwnProperty('popuptextcontent') || markerData.hasOwnProperty('popuphtmlcontent')){
							var popupConfig = {};
							if (markerData.hasOwnProperty('iconwidth') && markerData.hasOwnProperty('iconheight')){
								var markerWidth = parseInt(markerData.iconwidth.replace(/\D/g,''));
								var markerHeight = parseInt(markerData.iconheight.replace(/\D/g,''));
								var markerRadius = markerWidth / 2;
								var linearOffset = Math.round(Math.sqrt(0.5 * Math.pow(markerRadius, 2)));
								
								popupConfig = { offset: {
									'top': [0, 0],
									'top-left': [linearOffset, (markerHeight - markerRadius - linearOffset) * -1],
									'top-right': [-linearOffset, (markerHeight - markerRadius - linearOffset) * -1],
									'bottom': [0, -markerHeight],
									'bottom-left': [linearOffset, (markerHeight - markerRadius + linearOffset) * -1],
									'bottom-right': [-linearOffset, (markerHeight - markerRadius + linearOffset) * -1],
									'left': [markerRadius, (markerHeight - markerRadius) * -1],
									'right': [-markerRadius, (markerHeight - markerRadius) * -1]
								}};
							}
							
							const popup = new mapboxgl.Popup(popupConfig);
							
							if (markerData.hasOwnProperty('popuptextcontent')){
								popup.setText(markerData.popuptextcontent);
							}
							else{
								popup.setHTML(markerData.popuphtmlcontent);
							}
							
							popup._closeButton.style.display = 'block';
							
							marker.setPopup(popup);
						}
						
						marker.addTo(map);
					});
					
					<?php if ($fitbounds == 1){
						echo esc_html('map.fitBounds(bounds, { padding: 100 });');
					} ?>
				} catch (error) {
					console.error(error);
				}
			});
		}
	<?php

        $script = ob_get_clean();

        return $this->yatmogetDiv($height, $width) . $this->wrap_script($script, 'WPYatmoMapShortcode');
    }
}