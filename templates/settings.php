<?php
/**
 * Settings View
 * 
 * PHP Version 5.5
 * 
 * @category Admin
 * @author   Yatmo SRL
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$title = $plugin_data['Name'];
$description = __('A plugin for creating a Yatmo JS map with a shortcode. Don\'t worry, our map is free if you don\'t use more than 500 map loads per day ;-)', 'yatmo-map');
$version = $plugin_data['Version'];
?>
<div class="wrap">

<h1><?php echo esc_html($title); ?> <small>version: <?php echo esc_html($version); ?></small></h1>

<?php
/** START FORM SUBMISSION */

// validate nonce!
define('NONCE_NAME', 'yatmo-map-nonce');
define('NONCE_ACTION', 'yatmo-map-action');
if (!function_exists('yatmo_verify_nonce')) {
function yatmo_verify_nonce () {
    $verified = (
        isset($_POST[NONCE_NAME]) &&
        check_admin_referer(NONCE_ACTION, NONCE_NAME)
    );

    if (!$verified) {
        // side-effects can be fun?
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo esc_html('Sorry, your nonce did not verify', 'yatmo-map'); ?></p>
        </div>
        <?php
    }

    return $verified;
}
}
if (isset($_POST['submit']) && yatmo_verify_nonce()) {
    /* copy and overwrite $post for checkboxes */
    $form = $_POST;

    foreach ($settings->options as $name => $option) {
        if (!$option->type) continue;

        /* checkboxes don't get sent if not checked */
        if ($option->type === 'checkbox') {
            $form[$name] = isset($_POST[ $name ]) ? 1 : 0;
        }

        $value = trim( stripslashes( $form[$name]) );

        $settings->set($name, $value);
    }
?>
<div class="notice notice-success is-dismissible">
    <p><?php echo esc_html('Options Updated!', 'yatmo-map'); ?></p>
</div>
<?php
} elseif (isset($_POST['reset']) && yatmo_verify_nonce()) {
    $settings->reset();
?>
<div class="notice notice-success is-dismissible">
    <p><?php echo esc_html('Options have been reset to default values!', 'yatmo-map'); ?></p>
</div>
<?php
} elseif (isset($_POST['clear-geocoder-cache']) && yatmo_verify_nonce()) {
    include_once yatmoMapPluginDir . 'class.geocoder.php';
    Yatmo_Geocoder::yatmoRemoveCaches();
?>
<div class="notice notice-success is-dismissible">
    <p><?php echo esc_html('Location caches have been cleared!', 'yatmo-map'); ?></p>
</div>
<?php
}
/** END FORM SUBMISSION */

?>

<p><?php echo esc_html($description); ?></p>
<h3><?php echo esc_html('Found an issue?', 'yatmo-map') ?></h3>
<p><?php echo esc_html('Post it to ', 'yatmo-map') ?><b><?php echo esc_html('WordPress Support', 'yatmo-map') ?></b>: <a href="https://wordpress.org/support/plugin/yatmo-map/" target="_blank">Yatmo Map (WordPress)</a></p>

<div class="wrap">
    <div class="wrap">
    <form method="post">
        <?php wp_nonce_field(NONCE_ACTION, NONCE_NAME); ?>
        <div class="container">
            <h2><?php echo esc_html('Settings', 'yatmo-map'); ?></h2>
            <hr>
        </div>
    <?php
    
    foreach ($settings->options as $name => $option) {
        if (!$option->type) continue;
       
    ?>
    <div class="container">
        <label>
            <span class="label"><?php echo esc_html($option->display_name); ?></span>
            <span class="input-group">
            <?php
            $option->widget($name, $settings->get($name));
            ?>
            </span>
        </label>

        <?php
        if ($option->helptext) {
        ?>
        <div class="helptext">
            <p class="description"><?php  echo _e($option->helptext); ?></p>
        </div>
        <?php
        }
        ?>
    </div>
    <?php
    }
    ?>
    <div class="submit">
        <input type="submit" 
            name="submit" 
            id="submit" 
            class="button button-primary" 
            value="<?php echo esc_html('Save Changes', 'yatmo-map'); ?>">
    </div>

    </form>

    </div>
</div>
