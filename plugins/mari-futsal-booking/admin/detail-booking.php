<?php
/**
 * Halaman Detail Booking
 * Menampilkan info lenkgap 1 booking
 */

if(!defined('ABSPATH')){
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

global $wpdb;

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$booking_id) {
    wp_die('Booking id tidak valid.');
}

$booking_table = $wpdb->prefix . 'futsal_booking';
$lapangan_table = $wpdb->prefix . 'futsal_lapangan';
$jadwal_table = $wpdb->prefix . 'futsal_jadwal';

$query = "
    SELECT
        b.*,
        l.nama as lapangan_nama,
        l.jenis_lapangan,
        l.harga as lapangan_harga,
        j.jam_mulai,
        j.jam_selesai
    FROM $booking_table b
    LEFT JOIN $lapangan_table l ON b.lapangan_id = l.id
    LEFT JOIN $jadwal_table j ON b.jadwal_id = j.id
    WHERE b.id = %d
";

$booking = $wpdb->get_row($wpdb->prepare($query, $booking_id));

if (!$booking) {
    wp_die('Booking tidak ditemukan.');
}

$tanggal_formatted = date('d F Y', strtotime($booking->tanggal));
$created_formatted = date('d F Y, H:i', strtotime($booking->created_at));

$bulan_indonesia = array (
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
);

$tanggal_formatted = str_replace(array_keys($bulan_indonesia), array_values($bulan_indonesia), $tanggal_formatted);
$created_formatted = str_replace(array_keys($bulan_indonesia), array_values($bulan_indonesia), $created_formatted);
?>

<div class="wrap">
    <h1 class="wp-heading-inline" style="display:flex; justify-content:center; font-weight:bold">Detail Booking</h1>
    <hr class="wp-header-end">

    <div style="max-width: 800px; margin-top: 20px;">
        
        <!-- Kode Booking Card -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; text-align: center; margin-bottom: 20px;">
            <p style="margin: 0; font-size: 14px; opacity: 0.9;">Kode Booking</p>
            <h2 style="margin: 10px 0; font-size: 36px; letter-spacing: 2px; font-weight: bold;">
                <?php echo esc_html($booking->kode_booking); ?>
            </h2>
            <p style="margin: 0; font-size: 12px; opacity: 0.8;">
                Dibuat pada: <?php echo $created_formatted; ?> WIB
            </p>
        </div>

        <!-- Info Cards -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            
            <!-- Customer Info -->
            <div class="postbox">
                <div class="postbox-header">
                    <h2 class="hndle">👤 Informasi Customer</h2>
                </div>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th style="width: 40%;">Nama Lengkap:</th>
                            <td><strong><?php echo esc_html($booking->nama); ?></strong></td>
                        </tr>
                        <tr>
                            <th>No HP/WA:</th>
                            <td>
                                <strong><?php echo esc_html($booking->no_hp); ?></strong>
                                <br>
                                <a href="https://wa.me/62<?php echo ltrim($booking->no_hp, '0'); ?>" target="_blank" class="button button-small" style="margin-top: 5px;">
                                    📱 Chat WhatsApp
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Lapangan Info -->
            <div class="postbox">
                <div class="postbox-header">
                    <h2 class="hndle">🏟️ Informasi Lapangan</h2>
                </div>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th style="width: 40%;">Nama Lapangan:</th>
                            <td><strong><?php echo esc_html($booking->lapangan_nama); ?></strong></td>
                        </tr>
                        <tr>
                            <th>Jenis Lapangan:</th>
                            <td><?php echo esc_html($booking->jenis_lapangan); ?></td>
                        </tr>
                        <tr>
                            <th>Harga per Jam:</th>
                            <td>Rp <?php echo number_format($booking->lapangan_harga, 0, ',', '.'); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>

        <!-- Booking Details -->
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle">📅 Detail Booking</h2>
            </div>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th style="width: 30%;">Tanggal Main:</th>
                        <td>
                            <strong style="font-size: 16px; color: #2271b1;">
                                <?php echo $tanggal_formatted; ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th>Jam Main:</th>
                        <td>
                            <strong style="font-size: 16px; color: #2271b1;">
                                <?php echo date('H:i', strtotime($booking->jam_mulai)); ?> - 
                                <?php echo date('H:i', strtotime($booking->jam_selesai)); ?> WIB
                            </strong>
                            <span style="color: #666; margin-left: 10px;">
                                (Durasi: 1 jam)
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Pembayaran:</th>
                        <td>
                            <span style="font-size: 24px; font-weight: bold; color: #00a32a;">
                                Rp <?php echo number_format($booking->total_harga, 0, ',', '.'); ?>
                            </span>
                            <br>
                            <small style="color: #666;">* Pembayaran di tempat saat datang</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle">ℹ️ Informasi Tambahan</h2>
            </div>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th style="width: 30%;">Booking ID:</th>
                        <td><?php echo $booking->id; ?></td>
                    </tr>
                    <tr>
                        <th>Waktu Booking Dibuat:</th>
                        <td><?php echo $created_formatted; ?> WIB</td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php
                            $today = date('Y-m-d');
                            $booking_date = $booking->tanggal;
                            
                            if ($booking_date < $today) {
                                echo '<span style="display: inline-block; padding: 5px 15px; background: #ddd; color: #666; border-radius: 15px; font-size: 12px; font-weight: bold;">SELESAI</span>';
                            } elseif ($booking_date == $today) {
                                echo '<span style="display: inline-block; padding: 5px 15px; background: #00a32a; color: white; border-radius: 15px; font-size: 12px; font-weight: bold;">HARI INI</span>';
                            } else {
                                echo '<span style="display: inline-block; padding: 5px 15px; background: #2271b1; color: white; border-radius: 15px; font-size: 12px; font-weight: bold;">AKAN DATANG</span>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <a href="?page=mari-futsal-booking" class="button button-primary button-large">
                ← Kembali ke List Booking
            </a>
        </div>

    </div>
</div>

<style media="print">
    .wrap > h1 a,
    .wrap > div > div:last-child,
    #wpcontent,
    #wpfooter,
    #adminmenuback,
    #adminmenuwrap {
        display: none !important;
    }
    
    .wrap {
        margin: 0 !important;
    }
    
    .postbox {
        border: 1px solid #ddd !important;
        box-shadow: none !important;
    }
</style>