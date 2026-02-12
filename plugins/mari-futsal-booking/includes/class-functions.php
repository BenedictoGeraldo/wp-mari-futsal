<?php
/**
 * Helper Functions
 * Semua fungsi utility untuk get data, format, validasi, dll
 */

if (!defined('ABSPATH')) {
    exit;
}

class MF_Functions {
    public static function get_all_lapangan($status = 'aktif') {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_lapangan';

        if ($status !== null) {
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE status =  %s ORDER BY id ASC",
                $status
            ));
        }
            return $wpdb->get_results("SELECT * FROM $table ORDER BY id ASC");
    }

    public static function get_lapangan($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_lapangan';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ));
    }

    public static function get_all_jadwal() {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_jadwal';

        return $wpdb->get_results("SELECT * FROM $table ORDER BY jam_mulai ASC");
    }

    public static function get_jadwal($id){
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_jadwal';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $id
        ));
    }

    public static function get_available_jadwal($lapangan_id, $tanggal){
        global $wpdb;
        $table_jadwal = $wpdb->prefix . 'fitsal_jadwal';
        $table_booking = $wpdb->prefix . 'futsal_booking';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT j.* FROM $table_jadwal j
            WHERE j.id NOT IN (
                SELECT b.jadwal_id FROM $table_booking
                WHERE lapangan_id = %d AND tanggal = %s
                )
                ORDER BY j.jam_mulai ASC",
                $lapangan_id,
                $tanggal 
        ));
    }

    public static function get_all_bookings($limit = null) {
        global $wpdb;
        $table_booking = $wpdb->prefix . 'futsal_booking';
        $table_lapangan = $wpdb->prefix . 'futsal_lapangan';
        $table_jadwal = $wpdb->prefix . 'futsal_jadwal';

        $sql = "SELECT b.*, l.nama_lapangan as nama_lapangan, j.jam_mulai, j.jam_selesai
            FROM $table_booking b
            LEFT JOIN $table_lapangan l ON b.lapangan_id = l.id
            LEFT JOIN $table_jadwal j ON b.jadwal_id = j.id
            ORDER BY b.tanggal DESC, j.jam_mulai DESC";

        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }

        return $wpdb->get_results($sql);
    }

    public static function get_bookings_by_date($tanggal) {
        global $wpdb;
        $table_booking = $wpdb->prefix . 'futsal_booking';
        $table_lapangan = $wpdb->prefix . 'futsal_lapangan';
        $table_jadwal = $wpdb->prefix . 'futsal_jadwal';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT b.*, l.nama_lapangan as nama_lapangan, j.jam_mulai, j.jam_selesai
            FROM $table_booking b
            LEFT JOIN $table_lapangan l on b.lapangan_id = l.id
            LEFT JOIN $table_jadwal j on b.jadwal_id = j.id
            WHERE b.tanggal = %s
            ORDER BY j.jam_mulai ASC",
            $tanggal
        ));
    }

    public static function get_booking_by_kode($kode_booking) {
        global $wpdb;
        $table_booking = $wpdb->prefix . 'futsal_booking';
        $table_lapangan = $wpdb->prefix . 'futsal_lapangan';
        $table_jadwal = $wpdb->prefix . 'futsal_jadwal';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT b.*, l.nama_lapangan as nama_lapangan, l.jenis_lapangan, j.jam_mulai, j.jam_selesai
            FROM $table_booking b
            LEFT JOIN $table_lapangan l ON b.lapangan_id = l.id
            LEFT JOIN $table_jadwal j ON b.jadwal_id = j.id
            WHERE b.kode_booking = %s",
            $kode_booking
        ));
    }

    public static function generate_booking_code() {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_booking';

        do {
            $date = date('Ymd');
            $random = strtoupper(substr(md5(uniqid(rand(), true )), 0, 4));
            $kode = 'MF-' . $date . '-' . $random;

            $exists = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE kode_booking = %s",
                $kode
            ));
        } while ($exists > 0);
        return $kode;
    }

    public static function get_booking_count_today() {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_booking';
        $today = date('Y-m-d');

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE tanggal = %s",
            $today
        ));
    }

    public static function get_booking_count_week(){
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_booking';
        $start_week = date('Y-m-d', strtotime('monday this week'));
        $end_week = date('Y-m-d', strtotime('sunday this week'));

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE tanggal BETWEEN %s AND %s",
            $start_week,
            $end_week
        ));
    }

    public static function get_total_lapangan() {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_lapangan';

        return (int) $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'aktif'");
    }

    public static function get_total_jadwal() {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_jadwal';

        return (int) $wpdb->get_var("SELECT COUNT(*) FROM $table");
    }

    public static function format_rupiah($amount) {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public static function format_time($time) {
        return date('H:i', strtotime($time));
    }

    public static function format_date($date) {
        $bulan = array(
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        );

        $timestamp = strtotime($date);
        $day = date('d', $timestamp);
        $month = $bulan[date('n', $timestamp)];
        $year = date('Y', $timestamp);

        return $day . ' ' . $month . ' ' . $year;
    }

    public static function sanitize_phone($phone) {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }
    
    public static function is_slot_available($lapangan_id, $jadwal_id, $tanggal) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_booking';

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table
            WHERE lapangan_id = %d AND jadwal_id = %d AND tanggal = %s",
            $lapangan_id,
            $jadwal_id,
            $tanggal
        ));

        return $count == 0;
    }

    public static function verify_nonce($action, $nonce_field = 'mf_nonce') {
        if(!isset($_POST[$nonce_field])) {
            return false;
        }

        return wp_verify_nonce($_POST[$nonce_field], $action);
    }

    public static function current_user_can_manage() {
        return current_user_can('manage_options');
    }

    public static function sanitize_text($input) {
        return sanitize_text_field(trim($input));
    }

    public static function sanitize_number($input) {
        return absint($input);
    }

    public static function validate_required($value) {
        return !empty($value) && trim($value) !== '';
    }

    public static function set_flash_message($message, $type = 'success') {
        set_transient('mf_flash_message', array(
            'message' => $message,
            'type' => $type
        ), 45);
    }

    public static function get_flash_message() {
        $flash = get_transient('mf_flash_message');
        if($flash) {
            delete_transient('mf_flash_message');
            return $flash;
        } else {
            return false;
        }
    }

    public static function display_flash_message() {
        $flash = self::get_flash_message();
        if (!$flash) {
            return;
        }

        $class = 'notice notice-' . $flash['type'] . ' is-dismissible';
        printf('<div class="%s"><p>%s</p></div>',
            esc_attr($class),
            esc_html($flash['message'])
        );
    }

    public static function get_validation_errors() {
        return get_transient('mf_validation_errors') ?: array();
    }

    public static function set_validation_errors($errors) {
        set_transient('mf_validation_errors', $errors, 45);
    }

    public static function clear_validation_errors() {
        delete_transient('mf_validation_errors');
    }

    //add lapangan function
    public static function add_lapangan($data) {
        global $wpdb;
        $table = $wpdb->prefix. 'futsal_lapangan';

        $insert_data = array(
            'nama' => self::sanitize_text($data['nama']),
            'jenis_lapangan' => self::sanitize_text($data['jenis_lapangan']),
            'harga' => self::sanitize_number($data['harga']),
            'status' => self::sanitize_text($data['status']),
            'foto' => isset($data['foto']) ? self::sanitize_text($data['foto']) : null,
            'created_at' => current_time('mysql')
        );

        $result = $wpdb->insert($table, $insert_data);

        if ($result) {
            return $wpdb->insert_id;
        }
        return false;
    }

    //update lapangan function
    public static function update_lapangan($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_lapangan';

        $update_data = array (
            'nama' => self::sanitize_text($data['nama']),
            'jenis_lapangan' => self::sanitize_text($data['jenis_lapangan']),
            'harga' => self::sanitize_number($data['harga']),
            'status' => self::sanitize_text($data['status'])
        );

        if (isset($data['foto']) && !empty($data['foto'])) {
            $update_data['foto'] = self::sanitize_text($data['foto']);
        }

        $result = $wpdb->update(
            $table,
            $update_data,
            array('id' => $id),
            array('%s', '%s', '%d', '%s', '%s'),
            array('%d')
        );

        return $result !==false;
    }

    //delete lapangan image function
    public static function delete_lapangan_image($filename) {
        if (empty($filename)) {
            return false;
        }

        $upload_dir = wp_upload_dir();
        $file_path = $upload_dir['basedir'] . '/mari-futsal/' .$filename;

        if (file_exists($file_path)) {
            return unlink($file_path);
        }

        return false;
    }

    //delete lapangan function
    public static function delete_lapangan($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_lapangan';

        $lapangan = self::get_lapangan($id);

        $result = $wpdb->delete (
            $table,
            array('id' => $id),
            array('%d')
        );

        if ($result && $lapangan && !empty($lapangan->foto)) {
            self::delete_lapangan_image($lapangan->foto);
        }

        return $result !== false;
    }

    //validate lapangan data function
    public static function validate_lapangan_data($data, $mode = 'add') {
        $errors = array();

        if (!self::validate_required($data['nama'])) {
            $errors[] = 'nama lapangan wajib diisi.';
        } elseif (strlen(trim($data['nama'])) < 3) {
            $errors[] = 'nama lapangan minimal 3 karakter.';
        } elseif (strlen(trim($data['nama'])) > 100) {
            $errors[] = 'nama lapangan maksimal 100 karakter.';
        }

        if (!self::validate_required($data['jenis_lapangan'])) {
            $errors[] = 'jenis lapangan wajib diisi.';
        } else {
            $valid_jenis = array('Vinyl', 'Sintetis', 'Rumput');
            if (!in_array($data['jenis_lapangan'], $valid_jenis)) {
                $errors[] = 'jenis lapangan tidak valid.';
            }
        }

        if (!isset($data['harga']) || $data['harga'] === '') {
            $errors[] = 'harga wajib diisi';
        } elseif (!is_numeric($data['harga']) || $data['harga'] < 0) {
            $errors[] = 'harga harus berupa angka positif.';
        } elseif ($data['harga'] > 9999999) {
            $errors[] = 'harga maksimal 9.999.999.';
        }

        if (!self::validate_required($data['status'])) {
            $errors[] = 'status lapangan wajib diisi.';
        } else {
            $valid_status = array('aktif', 'nonaktif');
            if(!in_array($data['status'], $valid_status)) {
                $errors[] = 'status lapangan tidak valid.';
            }
        }

        return $errors;
    }

    //lapangan image upload function
    public static function handle_lapangan_image_upload($file) {
        if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return array('success' => false, 'error' => 'No file uploaded.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return array('success' => false, 'error' => 'Upload error occurred.');
        }

        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        $file_type = mime_content_type($file['tmp_name']);

        if (!in_array($file_type, $allowed_types)) {
            return array('success' => false, 'error' => 'Invalid tipe file.');
        }

        $max_size = 2 * 1024 * 1024;
        if ($file['size'] > $max_size) {
            return array('success' => false, 'error' => 'Ukuran file maksimal 2MB.');
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'lapangan_' . time() . '_' . uniqid() . '.' . $extension;


        $upload_dir = wp_upload_dir();
        $target_dir = $upload_dir['basedir'] . '/mari-futsal/';
        $target_file = $target_dir . $filename;

        if (!file_exists($target_dir)) {
            wp_mkdir_p($target_dir);
        }

        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return array(
                'success' => true,
                'filename' => $filename,
                'url' => $upload_dir['baseurl'] . '/mari-futsal/' . $filename
            );
        }

        return array('success' => false, 'error' => 'Gagal mengunggah file.');
    }

    public static function check_lapangan_has_bookings($lapangan_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_booking';

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE lapangan_id = %d",
            $lapangan_id
        ));

        return $count > 0;
    }
}