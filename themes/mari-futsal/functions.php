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