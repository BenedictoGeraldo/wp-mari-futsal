<?php
/**
 * Mari futsal child theme functions
 */

if (!defined('ABSPATH')) {
    exit;
}

function mari_futsal_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('child-style', get_stylesheet_uri(), array('parent-style'));
    wp_enqueue_style('tailwind-css', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
    
    // Enqueue custom frontend styles
    wp_enqueue_style(
        'mari-futsal-frontend',
        get_stylesheet_directory_uri() . '/assets/css/frontend-style.css',
        array('tailwind-css'),
        '1.1.0'
    );
}
add_action('wp_enqueue_scripts', 'mari_futsal_enqueue_styles');

function mari_futsal_booking_scripts() {
    if(is_page_template('page-booking.php') || is_page_template('page-lapangan.php')) {
        wp_enqueue_script(
            'mari-futsal-booking-js',
            get_stylesheet_directory_uri() . '/assets/js/booking.js',
            array('jquery'),
            '1.0.0',
            true
        );

        wp_localize_script('mari-futsal-booking-js', 'mfBooking', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('mf_public_nonce'),
            'homeurl' => home_url()
        ));
    }
}
add_action('wp_enqueue_scripts', 'mari_futsal_booking_scripts');

function mf_get_lapangan($id) {
    global $wpdb;
    $table = $wpdb->prefix . 'futsal_lapangan';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
}

function mf_get_all_lapangan_aktif(){
    global $wpdb;
    $table = $wpdb->prefix . 'futsal_lapangan';
    return $wpdb->get_results("SELECT * FROM $table WHERE status = 'aktif' ORDER BY id ASC"); 
}

function mf_get_jadwal($id) {
    global $wpdb;
    $table = $wpdb->prefix . 'futsal_jadwal';
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
}

function mf_get_all_jadwal() {
    global $wpdb;
    $table = $wpdb->prefix . 'futsal_jadwal';
    return $wpdb->get_results("SELECT * FROM $table ORDER BY jam_mulai ASC");
}

function mf_generate_booking_code() {
    $prefix = 'MF';
    $date = date('Ymd');
    $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
    return $prefix . $date . $random;
}

function mf_format_rupiah($angka) {
    return 'Rp' . number_format($angka, 0, ',', '.');
}

function mari_futsal_theme_support() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
}
add_action('after_setup_theme', 'mari_futsal_theme_support');

/**
 * Redirect homepage to Lapangan page
 * Day 7 - Homepage as Daftar Lapangan
 */
function mari_futsal_homepage_redirect() {
    // Check if we're on the homepage (not admin, not other pages)
    if ((is_front_page() || is_home()) && !is_page()) {
        // Get the page with template page-lapangan.php
        $lapangan_page = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-lapangan.php'
        ));
        
        if (!empty($lapangan_page)) {
            $current_url = home_url($_SERVER['REQUEST_URI']);
            $target_url = get_permalink($lapangan_page[0]->ID);
            
            // Only redirect if we're not already on the target page
            if ($current_url !== $target_url) {
                wp_safe_redirect($target_url);
                exit;
            }
        }
    }
}
add_action('template_redirect', 'mari_futsal_homepage_redirect');

/**
 * Remove specific menu items from navigation
 * Day 7 - Clean navigation - Hide for ALL users including admin
 */
function mari_futsal_remove_menu_items($items, $args) {
    // Remove menu items for ALL users (including admin)
    foreach ($items as $key => $item) {
        // Remove based on title
        if (in_array($item->title, array('Booking', 'Konfirmasi', 'Lapangan'))) {
            unset($items[$key]);
        }
    }
    
    return $items;
}
add_filter('wp_nav_menu_objects', 'mari_futsal_remove_menu_items', 10, 2);