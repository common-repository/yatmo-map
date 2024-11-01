<?php
/**
 * Used to generate an admin for Yatmo Map
 *
 * PHP Version 5.5
 * 
 * @category Admin
 * @author   Yatmo SRL
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * yatmo_map_Admin class
 */
class yatmo_map_Admin
{
    /**
     * Singleton Instance
     * 
     * @var yatmo_map_Admin $_instance
     */
    private static $_instance = null;

    /**
     * Singleton
     * 
     * @static
     * 
     * @return yatmo_map_Admin
     */
    public static function init()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * Instantiate the class
     */
    private function __construct()
    {
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_enqueue_scripts', array('yatmo_map', 'EnqueueAndRegister'));

        /* add settings to plugin page */
        add_filter('plugin_action_links_' . plugin_basename(yatmoMapPluginFile), array($this, 'plugin_action_links'));
    }

    /**
     * Admin init registers styles
     */
    public function admin_init() 
    {
        wp_register_style('yatmo_admin_stylesheet', plugins_url('style.css', yatmoMapPluginFile));
    }

    /**
     * Add admin menu page when user in admin area
     */
    public function admin_menu()
    {
        $pin = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzODQgNTEyIj48IS0tISBGb250IEF3ZXNvbWUgUHJvIDYuNC4wIGJ5IEBmb250YXdlc29tZSAtIGh0dHBzOi8vZm9udGF3ZXNvbWUuY29tIExpY2Vuc2UgLSBodHRwczovL2ZvbnRhd2Vzb21lLmNvbS9saWNlbnNlIChDb21tZXJjaWFsIExpY2Vuc2UpIENvcHlyaWdodCAyMDIzIEZvbnRpY29ucywgSW5jLiAtLT48cGF0aCBmaWxsPSIjOUNBMkE3IiBkPSJNMzg0IDE5MmMwIDg3LjQtMTE3IDI0My0xNjguMyAzMDcuMmMtMTIuMyAxNS4zLTM1LjEgMTUuMy00Ny40IDBDMTE3IDQzNSAwIDI3OS40IDAgMTkyQzAgODYgODYgMCAxOTIgMFMzODQgODYgMzg0IDE5MnoiLz48L3N2Zz4=';

        $admin = "manage_options";
        $author = "edit_posts";

        $main_link = 'yatmo-map';
		$main_page = array($this, "settings_page");

        add_menu_page("Yatmo Map", "Yatmo Map", $author, $main_link, $main_page, $pin);
        add_submenu_page("yatmo-map", __('Settings', 'yatmo-map'), __('Settings', 'yatmo-map'), $admin, "yatmo-map", array($this, "settings_page"));
    }

    /**
     * Main settings page includes form inputs
     */
    public function settings_page()
    {
        wp_enqueue_style('yatmo_admin_stylesheet');

        $settings = yatmoMapPluginSettings::init();
        $plugin_data = get_plugin_data(yatmoMapPluginFile);
        include 'templates/settings.php';
    }

    /**
     * Add settings link to the plugin on Installed Plugins page
     * 
     * @return array
     */
    public function plugin_action_links($links)
    {
        $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=yatmo-map') ) .'">Settings</a>';
        return $links;
    }
}