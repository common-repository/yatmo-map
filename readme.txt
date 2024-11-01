=== Simple Shortcode for Yatmo Map ===
Plugin Name: Simple Yatmo Map | Free map with points of interest!
Plugin URI: https://wordpress.org/plugins/yatmo-map/
Contributors: Yatmo SRL
Author: Yatmo SRL
Author URI: https://yatmo.com
Text Domain: yatmo-map
Domain Path: /languages
Tags: map, yatmo, pois, points of interest, maps
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tested up to: 6.3
Stable tag: 1.0.0

A simple shortcode for embedding Yatmo Maps (and its worldwide points of interest!) in any WordPress post, page or widget.

== Description ==

Very quick and simple to use Yatmo map plugin: with our without points of interest on the map, you will enjoy it!

Points of interest: a part of the POIs categories from OpenSteetMap can be shown on the map and we also added the shapes of the public transport lines and other interesting info like motorways entrances...

Put our map on your WordPress posts and pages simply and easily with a shortcode. Straight forward and easy to use! Ideal for real estate listings, contact page maps, maps showing delivery areas and many other uses!

You can use our map for free if you don't exceed 60,000 map loads per month, if you need more or if you want to access more options (like isochrones calculation, etc.): you can take a monthly subscription (no commitment, you stop when you want!) [here](https://yatmo.com/en/account/register). Do not forget to configure correctly your Yatmo account in this case (billing info, domain name, etc.).

You want to use our map on a real estate listing without giving the real location? We have an option to put a circle (with a randomly moved center of course) on the map instead of a classical pin! So check our anonymous option in the FAQ for that ;-)

FYI: our plugins are completely cookieless so don't worry about that, we won't add any cookies...

Note: this WordPress plugins relies on our script https://map.yatmo.com/map.js which creates the map and load the points of interest, etc.

It also relies on two 3rd party services:

* https://yatmo.com is needed to load the point of interest on the map, isochrones calculations, etc.
	* Terms of sales: https://yatmo.com/en/legal/termsofsales
	* Privacy policy: https://yatmo.com/en/legal/privacyandgdpr
	* Documentation: https://documentation.yatmo.com
* https://nominatim.openstreetmap.org
	* Limitations: https://operations.osmfoundation.org/policies/nominatim/
	* Privacy policy: https://wiki.osmfoundation.org/wiki/Privacy_Policy

Maps are displayed with the [yatmo-map] shortcode:

Simply create a **map** with (it will use the default location from the settings):

`[yatmo-map]`

(currently supporting one map per page!)

Lookup an address with:

`[yatmo-map address="Rue de la Loi 16, 1000 Bruxelles"]`

Know the latitude and longitude of a location? Use them (and a zoom level) with:

`[yatmo-map lat=50.850340 lng=4.351710 zoom=15]`

Add a **marker** under your map shortcode, like so:

`
[yatmo-map]
[yatmo-marker]
`

Want more? Make more (and fit the map to contain all of them):

`
[yatmo-map fitbounds]
[yatmo-marker lat=50.850340 lng=4.351710]
[yatmo-marker lat=50.784574 lng=4.254787]
`

They can also use an address instead of a latitude/longitude:

`
[yatmo-marker address="Rue de la Loi 16, 1000 Bruxelles"]
`

You can even add **popups** (with text content) with their titles (visible on mouse over):

`
[yatmo-map]
[yatmo-marker lat=50.850340 lng=4.351710 title="Hello world" popuptextcontent="Lorem ipsum dolor sit amet, consectetur adipiscing elit."]
`

= More =

Check out other examples on our documentation page: [https://documentation.yatmo.com/#wordpress_map](https://documentation.yatmo.com/#wordpress_map).

== Installation ==

1. Choose to add a new plugin, then click upload
2. Upload the yatmo-map zip
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Use the shortcodes in your pages or posts: e.g. `[yatmo-map]` and `[yatmo-marker]`

== Frequently Asked Questions ==

= Can I change the map style? =

Yes you can! Just use the settings page and choose your default style or override it thanks to the "style" option on the map shortcode. Like
`[yatmo-map style=2]`

= Can I change the marker color? =

Of course! Just use parameter "color" in the shortcode. Like
`
[yatmo-map]
[yatmo-marker lat=50.850340 lng=4.351710 color="yellow"]
`

= I want to use a map but not showing the precise location, how can I do that? =

Thanks to our plugin, it's possible!
You have to use the parameter "anonymous" in the marker shortcode. Like this:
`
[yatmo-map]
[yatmo-marker lat=50.850340 lng=4.351710 anonymous=1]
`
The precise location will be randomly modified on the server side and a circle will be added on the map.
It's also working fine with an address instead of lat/lng.
If you want to display information about the surroundings, do not hesitate to check our [other WordPress plugin](https://wordpress.org/plugins/yatmo-text/) for that!

= Can I add a message to a marker? =

Yes you can :)

`
[yatmo-map]
[yatmo-marker lat=50.850340 lng=4.351710 title="Hello world" popuptextcontent="Lorem ipsum dolor sit amet, consectetur adipiscing elit."]
`

The title is visible on mouse over and the content (HTML or text, you choose) is visible when clicked.

= I don't need them, can I disable the points of interest? =

Of course! You can do that in the plugin settings or in the shortcode:

`
[yatmo-map address="Rue de la Loi 16, 1000 Bruxelles" enable_pois="0"]
`

= I need more than 60,000 map loads per month and/or I want to use the isochrone feature =

You will need a license key for that: you just have to create an account [here](https://yatmo.com/en/account/register) and choose the right subscription for you (you can stop whenever you want, it's a simple Stripe monthly subscription!).

= Which languages are supported? =

Here is the list of the currently supported languages:

EN (English)
FR (Français)
IT (Italiano)
ZH (汉语)
DE (Deutsch)
ES (Español)
HI (हिन्दी)
JA (日本語)
PT (Português
NL (Nederlands)
CA (Català)
RU (Русский)

Which can be used has an option in the shortcode (or modified in your default settings):

`
[yatmo-map lat=50.850340 lng=4.351710 language=NL]
`

= Can I use a custom image instead of a colored pin for a marker? =

Yes, you can:
`
[yatmo-map]
[yatmo-marker lat=50.850340 lng=4.351710 iconurl="https://www.yourlink.com" iconwidth=32 iconheight=32]
`

= I need more functions!

Check our website (https://yatmo.com) and contact us to discuss about that!

== Screenshots ==

1. Simple default marker
2. Isochrones enabled (needs a paid subscription)
3. Points of interest disabled and custom map style
4. Info window on click on marker
5. Custom icon marker
6. "Anonymous" mode: marker with a circle with a center randomly adapted

== Changelog ==

= 1.0 =
First Version.
