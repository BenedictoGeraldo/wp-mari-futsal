<?php
/**
 * Plugin Name: Mari Futsal Booking System
 * Plugin URI: https://github.com/BenedictoGeraldo/wp-mari-futsal.git
 * Description: Sistem booking lapangan futsal berbasis WordPress dengan fitur CRUD lengkap untuk admin dan user-friendly booking interface.
 * Version: 1.0.0
 * Author: Beneeb3n
 * Text Domain: mari-futsal
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MF_VERSION', '1.0.0');
define('MF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MF_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class Mari_Futsal_Booking {
    
    /**
     * Instance of this class
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->define_hooks();
    }
    
    /**
     * Load required files
     */
    private function load_dependencies() {
        require_once MF_PLUGIN_DIR . 'includes/class-database.php';
        require_once MF_PLUGIN_DIR . 'includes/class-functions.php';
        
        if (is_admin()) {
            require_once MF_PLUGIN_DIR . 'admin/class-admin-menu.php';
        }
        
        require_once MF_PLUGIN_DIR . 'public/ajax-handlers.php';
    }
    
    /**
     * Define hooks
     */
    private function define_hooks() {
        // Activation & Deactivation - FIXED: koma bukan titik
        register_activation_hook(__FILE__, array('MF_Database', 'create_tables'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Enqueue scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'mari-futsal') === false) {
            return;
        }
        
        wp_enqueue_style('mf-admin-style', MF_PLUGIN_URL . 'assets/css/admin-style.css', array(), MF_VERSION);
        wp_enqueue_script('mf-admin-script', MF_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery'), MF_VERSION, true);
        
        // Localize script for AJAX
        wp_localize_script('mf-admin-script', 'mfAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mf_nonce')
        ));
    }
    
    /**
     * Enqueue public assets
     */
    public function enqueue_public_assets() {
        // Tailwind CSS via CDN
        wp_enqueue_style('tailwind-css', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
        
        wp_enqueue_script('mf-public-script', MF_PLUGIN_URL . 'assets/js/public-script.js', array('jquery'), MF_VERSION, true);
        
        // FIXED: mf_public_nonce (bukan md_public_nonce)
        wp_localize_script('mf-public-script', 'mfPublic', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mf_public_nonce')
        ));
    }
    
    /**
     * Deactivation
     */
    public function deactivate() {
        // Cleanup if needed (don't delete tables, just flush rewrite rules)
        flush_rewrite_rules();
    }
}

/**
 * Initialize the plugin
 */
function mari_futsal_booking_init() {
    return Mari_Futsal_Booking::get_instance();
}

// Start the plugin
mari_futsal_booking_init();