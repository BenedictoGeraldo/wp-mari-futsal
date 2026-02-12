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

    public static function verify_nonce($action, $nonce_field = '_wpnonce') {
        if(!isset($_POST[$nonce_field]) && !isset($_GET['_wpnonce'])) {
            return false;
        }

        $nonce = isset($_POST[$nonce_field]) ? $_POST[$nonce_field] : $_GET['_wpnonce'];
        return wp_verify_nonce($nonce, $action);
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
        // Allow HTML in message for better error display
        printf('<div class="%s"><p>%s</p></div>',
            esc_attr($class),
            wp_kses_post($flash['message'])
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

    // ========================================
    // JADWAL (SLOT WAKTU) FUNCTIONS - Day 4
    // ========================================

    /**
     * Validate jadwal data
     */
    public static function validate_jadwal_data($data, $mode = 'add', $jadwal_id = null) {
        $errors = array();

        // Normalize time format (remove seconds if present, trim spaces)
        if (isset($data['jam_mulai'])) {
            $data['jam_mulai'] = trim($data['jam_mulai']);
            // Convert HH:MM:SS to HH:MM
            if (preg_match('/^(\d{2}):(\d{2}):\d{2}$/', $data['jam_mulai'], $matches)) {
                $data['jam_mulai'] = $matches[1] . ':' . $matches[2];
            }
        }
        
        if (isset($data['jam_selesai'])) {
            $data['jam_selesai'] = trim($data['jam_selesai']);
            // Convert HH:MM:SS to HH:MM
            if (preg_match('/^(\d{2}):(\d{2}):\d{2}$/', $data['jam_selesai'], $matches)) {
                $data['jam_selesai'] = $matches[1] . ':' . $matches[2];
            }
        }

        // Validasi jam_mulai
        if (!self::validate_required($data['jam_mulai'])) {
            $errors[] = 'Jam mulai wajib diisi.';
        } elseif (!preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $data['jam_mulai'])) {
            $errors[] = 'Format jam mulai tidak valid (' . htmlspecialchars($data['jam_mulai']) . '). Gunakan format HH:MM (contoh: 08:00 atau 20:30).';
        }

        // Validasi jam_selesai
        if (!self::validate_required($data['jam_selesai'])) {
            $errors[] = 'Jam selesai wajib diisi.';
        } elseif (!preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $data['jam_selesai'])) {
            $errors[] = 'Format jam selesai tidak valid (' . htmlspecialchars($data['jam_selesai']) . '). Gunakan format HH:MM (contoh: 09:00 atau 21:30).';
        }

        // Validasi jam_selesai > jam_mulai
        if (empty($errors)) {
            if (strtotime($data['jam_selesai']) <= strtotime($data['jam_mulai'])) {
                $errors[] = 'Jam selesai (' . $data['jam_selesai'] . ') harus lebih besar dari jam mulai (' . $data['jam_mulai'] . ').';
            }
        }

        // Validasi overlap
        if (empty($errors)) {
            $overlap = self::check_jadwal_overlap($data['jam_mulai'], $data['jam_selesai'], $jadwal_id);
            if ($overlap) {
                $errors[] = 'Slot waktu <strong>' . $data['jam_mulai'] . ' - ' . $data['jam_selesai'] . 
                           '</strong> bertabrakan dengan slot yang sudah ada: <strong>' . 
                           date('H:i', strtotime($overlap->jam_mulai)) . ' - ' . 
                           date('H:i', strtotime($overlap->jam_selesai)) . '</strong>.';
            }
        }

        return $errors;
    }

    /**
     * Check jadwal overlap
     */
    public static function check_jadwal_overlap($jam_mulai, $jam_selesai, $exclude_id = null) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_jadwal';

        // Normalize time format to HH:MM:SS for comparison
        if (!preg_match('/:.*:/', $jam_mulai)) {
            $jam_mulai .= ':00';
        }
        if (!preg_match('/:.*:/', $jam_selesai)) {
            $jam_selesai .= ':00';
        }

        $sql = "SELECT * FROM $table WHERE (
            (jam_mulai < %s AND jam_selesai > %s) OR
            (jam_mulai < %s AND jam_selesai > %s) OR
            (%s <= jam_mulai AND %s >= jam_selesai)
        )";

        if ($exclude_id) {
            $sql .= $wpdb->prepare(" AND id != %d", $exclude_id);
        }

        return $wpdb->get_row($wpdb->prepare(
            $sql,
            $jam_selesai, $jam_mulai, // overlap start
            $jam_selesai, $jam_selesai, // overlap end
            $jam_mulai, $jam_selesai // contains
        ));
    }

    /**
     * Add jadwal
     */
    public static function add_jadwal($data) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_jadwal';

        // Normalize time format to HH:MM:SS for database
        $jam_mulai = $data['jam_mulai'];
        if (!preg_match('/:.*:/', $jam_mulai)) {
            $jam_mulai .= ':00'; // Add seconds if not present
        }
        
        $jam_selesai = $data['jam_selesai'];
        if (!preg_match('/:.*:/', $jam_selesai)) {
            $jam_selesai .= ':00'; // Add seconds if not present
        }

        $insert_data = array(
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai,
            'created_at' => current_time('mysql')
        );

        $result = $wpdb->insert($table, $insert_data);

        if ($result) {
            return $wpdb->insert_id;
        }
        return false;
    }

    /**
     * Update jadwal
     */
    public static function update_jadwal($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_jadwal';

        // Normalize time format to HH:MM:SS for database
        $jam_mulai = $data['jam_mulai'];
        if (!preg_match('/:.*:/', $jam_mulai)) {
            $jam_mulai .= ':00'; // Add seconds if not present
        }
        
        $jam_selesai = $data['jam_selesai'];
        if (!preg_match('/:.*:/', $jam_selesai)) {
            $jam_selesai .= ':00'; // Add seconds if not present
        }

        $update_data = array(
            'jam_mulai' => $jam_mulai,
            'jam_selesai' => $jam_selesai
        );

        $result = $wpdb->update(
            $table,
            $update_data,
            array('id' => $id),
            array('%s', '%s'),
            array('%d')
        );

        return $result !== false;
    }

    /**
     * Delete jadwal
     */
    public static function delete_jadwal($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_jadwal';

        $result = $wpdb->delete(
            $table,
            array('id' => $id),
            array('%d')
        );

        return $result !== false;
    }

    /**
     * Get booking count by jadwal
     */
    public static function get_booking_count_by_jadwal($jadwal_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_booking';

        return (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE jadwal_id = %d",
            $jadwal_id
        ));
    }

    /**
     * Calculate slot duration in minutes
     */
    public static function calculate_slot_duration($jam_mulai, $jam_selesai) {
        $start = strtotime($jam_mulai);
        $end = strtotime($jam_selesai);
        $diff = $end - $start;
        return round($diff / 60);
    }
}