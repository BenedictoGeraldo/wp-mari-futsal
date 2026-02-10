<?php

/**
 * Admin Menu
 *
 * Register semua menu admin di sidebar Wordpress
 */

if(!defined('ABSPATH')) {
    exit;
}

class MF_Admin_Menu {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu(){
        add_menu_page(
            'Mari Futsal',
            'Mari Futsal',
            'manage_options',
            'mari-futsal',
            array($this, 'dashboard_page'),
            'dashicons-calendar-alt',
            30
        );

        //submenu dashboard
        add_submenu_page(
            'mari-futsal',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'mari-futsal',
            array($this, 'dashboard_page')
        );

        //submenu kelola lapangan
        add_submenu_page(
            'mari-futsal',
            'Kelola Lapangan',
            'Kelola Lapangan',
            'manage_options',
            'mari-futsal-lapangan',
            array($this, 'lapangan_page')
        );

        //submenu kelola jadwal
        add_submenu_page(
            'mari-futsal',
            'Kelola Jadwal',
            'Kelola Jadwal',
            'manage_options',
            'mari-futsal-jadwal',
            array($this, 'jadwal_page')
        );

        //submenu kelola booking
        add_submenu_page(
            'mari-futsal',
            'Kelola Booking',
            'Kelola Booking',
            'manage_options',
            'mari-futsal-booking',
            array($this, 'booking_page')
        );
    }

    public function dashboard_page(){
        require_once MF_PLUGIN_DIR . 'admin/dashboard.php';
    }

    public function lapangan_page(){
        require_once MF_PLUGIN_DIR . 'admin/lapangan.php';
    }

    public function jadwal_page(){
        require_once MF_PLUGIN_DIR . 'admin/jadwal.php';
    }

    public function booking_page(){
        require_once MF_PLUGIN_DIR . 'admin/booking.php';
    }
}

new MF_Admin_Menu();