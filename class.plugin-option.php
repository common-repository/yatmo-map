<?php
/** 
 * yatmoMapPluginOption
 * 
 * Store values; render widgets
 * 
 * PHP Version 5.5
 * 
 * @category Shortcode
 * @author   Yatmo SRL
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * yatmoMapPluginOption
 */
class yatmoMapPluginOption
{
    /**
     * Default Value
     * 
     * @var varies $default
     */
    public $default = '';
    
    /**
     * Input type ex: ('text', 'select', 'checkbox')
     * 
     * @var string $type 
     */
    public $type;
    
    /**
     * Optional used for select; maybe checkbox/radio
     * 
     * @var array $options
     */
    public $options = array();

    /**
     * Optional used for label under input
     * 
     * @var string $helptext
     */
    public $helptext = '';

    /**
     * Instantiate class
     * 
     * @param array $details A list of options
     */
    function __construct($details = array())
    {
        if (!$details) {
            // just an empty db entry (for now)
            // nothing to store, nothing to render
            return;
        }

        $option_filter = array(
            'display_name'     =>     FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'default'          =>     null,
            'type'             =>     FILTER_SANITIZE_FULL_SPECIAL_CHARS,
            'min'              =>     "",
            'max'              =>     "",
            'step'             =>     "",
            'options'          =>     array(
                'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'flags'  => FILTER_FORCE_ARRAY
            ),
            'helptext'         =>     null
        );

        // get matching keys only
        $details = array_intersect_key($details, $option_filter);

        // apply filter
        $details = filter_var_array($details, $option_filter);

        foreach ($details as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Renders a widget
     * 
     * @param string $name  widget name
     * @param varies $value widget value
     * 
     * @return HTML
     */
    function widget ($name, $value) 
    {
        switch ($this->type) {
        case 'text':
            ?>
            <input 
    class="full-width" 
    name="<?php echo esc_attr($name); ?>" 
    type="<?php echo esc_attr($this->type); ?>" 
    id="<?php echo esc_attr($name); ?>" 
    value="<?php echo esc_attr($value); ?>" 
/>
            <?php
            break;

        
        case 'number':
            ?>
            <input 
    class="full-width" 
    min="<?php echo isset($this->min) ? esc_attr($this->min) : ''; ?>"
    max="<?php echo isset($this->max) ? esc_attr($this->max) : ''; ?>"
    step="<?php echo isset($this->step) ? esc_attr($this->step) : 'any'; ?>"
    name="<?php echo esc_attr($name); ?>" 
    type="<?php echo esc_attr($this->type); ?>" 
    id="<?php echo esc_attr($name); ?>" 
    value="<?php echo esc_attr($value); ?>" 
/>


            <?php
            break;
            
        case 'textarea':
            ?>
            <textarea 
    id="<?php echo esc_attr($name); ?>"
    class="full-width" 
    name="<?php echo esc_attr($name); ?>"><?php echo esc_textarea($value); ?></textarea>


            <?php
            break;

        case 'checkbox':
            ?>
            <input 
    class="checkbox" 
    name="<?php echo esc_attr($name); ?>" 
    type="checkbox" 
    id="<?php echo esc_attr($name); ?>"
    <?php checked($value); ?> 
/>

            <?php
            break;

        case 'select':
            ?>
        <select id="<?php echo esc_attr($name); ?>"
            name="<?php echo esc_attr($name); ?>"
            class="full-width">
        <?php
        foreach ($this->options as $o => $n) {
        ?>
            <option value="<?php echo esc_html($o); ?>"<?php if ($value == $o) echo ' selected' ?>>
                <?php echo esc_html($n); ?>
            </option>
        <?php
        }
        ?>
        </select>
                <?php
            break;
        default:
            ?>
        <div>No option type chosen for <?php echo esc_attr($name); ?> with value <?php echo esc_attr($value); ?></div>
            <?php
            break;
        }
    }
}