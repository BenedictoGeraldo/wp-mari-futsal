<?php
/**
 * Database Handler
 * Handle semua operasi database: create tables, insert dummy data
 */

if (!defined('ABSPATH')) {
    exit;
}

class MF_Database {
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();

        $table_lapangan = $wpdb->prefix . 'futsal_lapangan';
        $table_jadwal = $wpdb->prefix . 'futsal_jadwal';
        $table_booking = $wpdb->prefix . 'futsal_booking';

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql_lapangan = "CREATE TABLE $table_lapangan (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            nama varchar(255) NOT NULL,
            jenis_lapangan varchar(100) NOT NULL,
            harga int(11) NOT NULL,
            status enum('aktif','nonaktif') DEFAULT 'aktif',
            foto varchar(255) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id) 
            ) $charset_collate;";

        $sql_jadwal = "CREATE TABLE $table_jadwal (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            jam_mulai time NOT NULL,
            jam_selesai time NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
            ) $charset_collate;";

        $sql_booking = "CREATE TABLE $table_booking (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lapangan_id bigint(20) NOT NULL,
            jadwal_id bigint(20) NOT NULL,
            tanggal date NOT NULL,
            nama varchar(255) NOT NULL,
            no_hp varchar(20) NOT NULL,
            kode_booking varchar(50) NOT NULL,
            total_harga int(11) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY unique_booking (lapangan_id, jadwal_id, tanggal),
            UNIQUE KEY kode_booking (kode_booking),
            KEY lapangan_id (lapangan_id),
            KEY jadwal_id (jadwal_id)
            ) $charset_collate;";

        dbDelta($sql_lapangan);
        dbDelta($sql_jadwal);
        dbDelta($sql_booking);
        

        self::insert_default_jadwal();
        self::insert_dummy_lapangan();

        update_option('mf_db_version', MF_VERSION);
    }

    private static function insert_default_jadwal() {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_jadwal';

        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        $slots = array(
            array('08:00:00', '09:00:00'),
            array('09:00:00', '10:00:00'),
            array('10:00:00', '11:00:00'),
            array('11:00:00', '12:00:00'),
            array('12:00:00', '13:00:00'),
            array('13:00:00', '14:00:00'),
            array('14:00:00', '15:00:00'),
            array('15:00:00', '16:00:00'),
            array('16:00:00', '17:00:00'),
            array('17:00:00', '18:00:00'),
            array('18:00:00', '19:00:00'),
            array('19:00:00', '20:00:00'),
            array('20:00:00', '21:00:00'),
            array('21:00:00', '22:00:00'),
        );

        foreach ($slots as $slot) {
            $wpdb->insert($table, array(
                'jam_mulai' => $slot[0],
                'jam_selesai' => $slot[1]
            ));
        }
    }

    private static function insert_dummy_lapangan() {
        global $wpdb;
        $table = $wpdb->prefix . 'futsal_lapangan';

        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        $dummy_data = array(
            array(
                'nama' => 'Lapangan A',
                'jenis_lapangan' => 'Vinyl',
                'harga' => 120000,
                'status' => 'aktif'
            ),
            
            array(
                'nama' => 'Lapangan B',
                'jenis_lapangan' => 'Vinyl',
                'harga' => 120000,
                'status' => 'aktif'
            ),

            array(
                'nama' => 'Lapangan C',
                'jenis_lapangan' => 'Sintetis',
                'harga' => 100000,
                'status' => 'aktif'
            ),
        );

        foreach ($dummy_data as $data) {
            $wpdb->insert($table, $data);
        }
    }

    public static function drop_tables(){
        global $wpdb;

        $table_booking = $wpdb->prefix . 'futsal_booking';
        $table_jadwal = $wpdb->prefix . 'futsal_jadwal';
        $table_lapangan = $wpdb->prefix . 'futsal_lapangan';

        $wpdb->query("DROP TABLE IF EXISTS $table_booking");
        $wpdb->query("DROP TABLE IF EXISTS $table_jadwal");
        $wpdb->query("DROP TABLE IF EXISTS $table_lapangan");

        delete_option('mf_db_version');
    }
}