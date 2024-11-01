<?php
/**
* Geocoder
*
* calls the specific geocoder function
*
*/

class Yatmo_Geocoder {
    /**
    * Geocoder should return this on error/not found
    * @var array $not_found
    */
    private $not_found = array('lat' => 0, 'lng' => 0);
    /**
    * Latitude
    * @var float $lat
    */
    public $lat = 0;
    /**
    * Longitude
    * @var float $lng
    */
    public $lng = 0;

    /**
    * new Geocoder from address
    *
    * handles url encoding and caching
    *
    * @param string $address the requested address to look up
    * @return NOTHING
    */
    public function __construct ($address) {
        $settings = yatmoMapPluginSettings::init();
        // trim all quotes (even smart) from address
        $address = trim($address, '\'"”');
        $address = urlencode( $address );
        
        $geocoder = $settings->get('geocoder');

        $cached_address = 'yatmo_' . $geocoder . '_' . $address;

        /* retrieve cached geocoded location */
        $found_cache = get_option( $cached_address );

        if ( $found_cache ) {
            $location = $found_cache;
        } else {
            try {
                $location = (Object) $this->osmGeocode( $address );

                /* add location */
                add_option($cached_address, $location);

                /* add option key to locations for clean up purposes */
                $locations = get_option('yatmo_geocoded_locations', array());
                array_push($locations, $cached_address);
                update_option('yatmo_geocoded_locations', $locations);
            } catch (Exception $e) {
                // failed
                $location = $this->not_found;
            }
        }

        if (isset($location->lat) && isset($location->lng)) {
            $this->lat = $location->lat;
            $this->lng = $location->lng;
        }
    }

    /**
    * Removes location caches
    */
    public static function yatmoRemoveCaches () {
        $addresses = get_option('yatmo_geocoded_locations', array());
        foreach ($addresses as $address) {
            delete_option($address);
        }
        delete_option('yatmo_geocoded_locations');
    }

    /**
    * Used by geocoders to make requests via curl or file_get_contents
    *
    * includes a try/catch
    *
    * @param string $url    the urlencoded request url
    * @return varies object from API or null (failed)
    */
    private function yatmoGetUrl( $url ) {
        $referer = get_site_url();

        if (in_array('curl', get_loaded_extensions())) {
            /* try curl */
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_REFERER, $referer);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

            // Replace cURL code
            //$response = curl_exec($ch);
            // ...

            // Equivalent code using HTTP API
            $response = wp_remote_get($url);
            if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            // Process the response
            // ...
            }
            curl_close($ch);

            return $body;
        } else if (ini_get('allow_url_fopen')) {
                    /* try file get contents */

                    $opts = array(
                        'http' => array(
                            'header' => array("Referer: $referer\r\n")
                        )
                    );
                    $context = stream_context_create($opts);

                    return false;
        }

        $error_msg = 'Could not get url: ' . $url;
        throw new Exception( $error_msg );
    }

    /**
    * OpenStreetMap geocoder Nominatim (https://nominatim.openstreetmap.org/)
    *
    * @param string $address    the urlencoded address to look up
    * @return varies object from API or null (failed)
    */

    private function osmGeocode ( $address ) {
        $geocode_url = 'https://nominatim.openstreetmap.org/?format=json&limit=1&q=';
        $geocode_url .= $address;
        $json = $this->yatmoGetUrl($geocode_url);
        $json = json_decode($json);

        if (isset($json[0]->lat) && isset($json[0]->lon)) {
            return (Object) array(
                'lat' => $json[0]->lat,
                'lng' => $json[0]->lon,
            );
        } else {
            return false;
        }
    }
}