<?php
/**
 * Public AJAX Handlers untuk booking system
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * AJAX: Get available slots for specific date and lapangan
 */
add_action('wp_ajax_mf_get_available_slots', 'mf_ajax_get_available_slots');
add_action('wp_ajax_nopriv_mf_get_available_slots', 'mf_ajax_get_available_slots');

function mf_ajax_get_available_slots() {
    // Verify nonce
    check_ajax_referer('mf_public_nonce', 'nonce');
    
    global $wpdb;
    
    $lapangan_id = isset($_POST['lapangan_id']) ? intval($_POST['lapangan_id']) : 0;
    $tanggal = isset($_POST['tanggal']) ? sanitize_text_field($_POST['tanggal']) : '';
    
    if (!$lapangan_id || !$tanggal) {
        wp_send_json_error('Parameter tidak lengkap');
    }
    
    // Validate date format
    $date_check = DateTime::createFromFormat('Y-m-d', $tanggal);
    if (!$date_check || $date_check->format('Y-m-d') !== $tanggal) {
        wp_send_json_error('Format tanggal tidak valid');
    }
    
    // Get all jadwal
    $jadwal_table = $wpdb->prefix . 'futsal_jadwal';
    $jadwal_list = $wpdb->get_results("SELECT * FROM $jadwal_table ORDER BY jam_mulai ASC");
    
    if (!$jadwal_list) {
        wp_send_json_error('Belum ada jadwal tersedia');
    }
    
    // Get booked slots untuk tanggal dan lapangan ini
    $booking_table = $wpdb->prefix . 'futsal_booking';
    $booked_slots = $wpdb->get_col($wpdb->prepare(
        "SELECT jadwal_id FROM $booking_table WHERE lapangan_id = %d AND tanggal = %s",
        $lapangan_id,
        $tanggal
    ));
    
    // Build response
    $slots = array();
    foreach ($jadwal_list as $jadwal) {
        $slots[] = array(
            'id' => $jadwal->id,
            'jam_mulai' => date('H:i', strtotime($jadwal->jam_mulai)),
            'jam_selesai' => date('H:i', strtotime($jadwal->jam_selesai)),
            'available' => !in_array($jadwal->id, $booked_slots)
        );
    }
    
    wp_send_json_success($slots);
}

/**
 * AJAX: Submit booking
 */
add_action('wp_ajax_mf_submit_booking', 'mf_ajax_submit_booking');
add_action('wp_ajax_nopriv_mf_submit_booking', 'mf_ajax_submit_booking');

function mf_ajax_submit_booking() {
    // Verify nonce
    check_ajax_referer('mf_public_nonce', 'nonce');
    
    global $wpdb;
    
    // Get and sanitize input
    $lapangan_id = isset($_POST['lapangan_id']) ? intval($_POST['lapangan_id']) : 0;
    $jadwal_id = isset($_POST['jadwal_id']) ? intval($_POST['jadwal_id']) : 0;
    $tanggal = isset($_POST['tanggal']) ? sanitize_text_field($_POST['tanggal']) : '';
    $nama = isset($_POST['nama']) ? sanitize_text_field($_POST['nama']) : '';
    $no_hp = isset($_POST['no_hp']) ? sanitize_text_field($_POST['no_hp']) : '';
    $total_harga = isset($_POST['total_harga']) ? intval($_POST['total_harga']) : 0;
    
    // Validation
    if (!$lapangan_id || !$jadwal_id || !$tanggal || !$nama || !$no_hp || !$total_harga) {
        wp_send_json_error('Semua field harus diisi');
    }
    
    // Validate phone number
    if (!preg_match('/^[0-9]{10,13}$/', $no_hp)) {
        wp_send_json_error('Nomor HP tidak valid');
    }
    
    // Validate date
    $date_check = DateTime::createFromFormat('Y-m-d', $tanggal);
    if (!$date_check || $date_check->format('Y-m-d') !== $tanggal) {
        wp_send_json_error('Format tanggal tidak valid');
    }
    
    // Check if date is not in the past
    if ($tanggal < date('Y-m-d')) {
        wp_send_json_error('Tidak bisa booking untuk tanggal yang sudah lewat');
    }
    
    // Check lapangan exists and active
    $lapangan_table = $wpdb->prefix . 'futsal_lapangan';
    $lapangan = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $lapangan_table WHERE id = %d AND status = 'aktif'",
        $lapangan_id
    ));
    
    if (!$lapangan) {
        wp_send_json_error('Lapangan tidak tersedia');
    }
    
    // Check jadwal exists
    $jadwal_table = $wpdb->prefix . 'futsal_jadwal';
    $jadwal = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $jadwal_table WHERE id = %d",
        $jadwal_id
    ));
    
    if (!$jadwal) {
        wp_send_json_error('Jadwal tidak valid');
    }
    
    // Check slot availability (prevent race condition)
    $booking_table = $wpdb->prefix . 'futsal_booking';
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $booking_table WHERE lapangan_id = %d AND jadwal_id = %d AND tanggal = %s",
        $lapangan_id,
        $jadwal_id,
        $tanggal
    ));
    
    if ($existing > 0) {
        wp_send_json_error('Maaf, slot ini sudah dibooking oleh orang lain. Silakan pilih slot lain.');
    }
    
    // Generate unique booking code
    $kode_booking = mf_generate_unique_booking_code();
    
    // Insert booking
    $inserted = $wpdb->insert(
        $booking_table,
        array(
            'lapangan_id' => $lapangan_id,
            'jadwal_id' => $jadwal_id,
            'tanggal' => $tanggal,
            'nama' => $nama,
            'no_hp' => $no_hp,
            'kode_booking' => $kode_booking,
            'total_harga' => $total_harga,
            'created_at' => current_time('mysql')
        ),
        array('%d', '%d', '%s', '%s', '%s', '%s', '%d', '%s')
    );
    
    if ($inserted === false) {
        wp_send_json_error('Gagal menyimpan booking. Silakan coba lagi.');
    }
    
    wp_send_json_success(array(
        'kode_booking' => $kode_booking,
        'message' => 'Booking berhasil!'
    ));
}

/**
 * Helper: Generate unique booking code
 */
function mf_generate_unique_booking_code() {
    global $wpdb;
    $booking_table = $wpdb->prefix . 'futsal_booking';
    
    do {
        $prefix = 'MF';
        $date = date('Ymd');
        $random = strtoupper(substr(md5(uniqid(rand(), true)), 0, 4));
        $kode = $prefix . $date . $random;
        
        // Check if exists
        $exists = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $booking_table WHERE kode_booking = %s",
            $kode
        ));
    } while ($exists > 0);
    
    return $kode;
}