<?php
/**
 * AJAX handlers
 * handle semua ajax request dari frontend user
 */

if (!defined('ABSPATH')){
    exit;
}

add_action('wp_ajax_mf_get_available_slots', 'mf_ajax_get_available_slots');
add_action('wp_ajax_nopriv_mf_get_available_slots', 'mf_ajax_get_available_slots');

function mf_ajax_fet_available_slots(){
    check_ajax_referer('mf_public_nonce', 'nonce');

    $lapangan_id = isset($_POST['lapangan_id']) ? intval($_POST['lapangan_id']) : 0;
    $tanggal = isset($_POST['tanggal']) ? sanitize_text_field($_POST['tanggal']) : '';

    if (!$lapangan_id || !$tanggal) {
        wp_send_json_error(array('message' => 'invalid parameters'));
    }

    $available_slots = MF_Functions::get_available_jadwal($lapangan_id, $tanggal);

    wp_send_json_success(array('slots' => $available_slots));
}

add_action('wp_ajax_mf_submit_booking', 'mf_ajax_submit_booking');
add_action('wp_ajax_nopriv_mf_submit_booking', 'mf_ajax_submit_booking');

function mf_ajax_submit_booking(){
    check_ajax_referer('mf_public_nonce', 'nonce');

    wp_send_json_error(array('message' => 'Not Implemented yet'));
}