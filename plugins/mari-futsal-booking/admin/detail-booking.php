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

<div class="wrap mf-detail-booking">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-tickets-alt"></span>
        Detail Booking
    </h1>
    <hr class="wp-header-end">

    <div class="mf-detail-container">
        
        <!-- Kode Booking Card -->
        <div class="mf-booking-code-card">
            <p class="mf-booking-label">Kode Booking</p>
            <h2 class="mf-booking-code">
                <?php echo esc_html($booking->kode_booking); ?>
            </h2>
            <p class="mf-booking-timestamp">
                Dibuat pada: <?php echo $created_formatted; ?> WIB
            </p>
        </div>

        <!-- Info Cards -->
        <div class="mf-info-grid">
            
            <!-- Customer Info -->
            <div class="mf-card postbox">
                <div class="postbox-header">
                    <h2 class="hndle">
                        <span class="dashicons dashicons-admin-users"></span>
                        Informasi Customer
                    </h2>
                </div>
                <div class="inside">
                    <table class="mf-detail-table">
                        <tr>
                            <th>Nama Lengkap:</th>
                            <td><strong><?php echo esc_html($booking->nama); ?></strong></td>
                        </tr>
                        <tr>
                            <th>No HP/WA:</th>
                            <td>
                                <strong><?php echo esc_html($booking->no_hp); ?></strong>
                                <br>
                                <a href="https://wa.me/62<?php echo ltrim($booking->no_hp, '0'); ?>" target="_blank" class="mf-btn mf-btn-success mf-btn-small" style="margin-top: 8px;">
                                    <span class="dashicons dashicons-whatsapp"></span>
                                    Chat WhatsApp
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Lapangan Info -->
            <div class="mf-card postbox">
                <div class="postbox-header">
                    <h2 class="hndle">
                        <span class="dashicons dashicons-admin-multisite"></span>
                        Informasi Lapangan
                    </h2>
                </div>
                <div class="inside">
                    <table class="mf-detail-table">
                        <tr>
                            <th>Nama Lapangan:</th>
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
        <div class="mf-card postbox">
            <div class="postbox-header">
                <h2 class="hndle">
                    <span class="dashicons dashicons-calendar-alt"></span>
                    Detail Booking
                </h2>
            </div>
            <div class="inside">
                <table class="mf-detail-table">
                    <tr>
                        <th>Tanggal Main:</th>
                        <td>
                            <strong class="mf-highlight-text">
                                <?php echo $tanggal_formatted; ?>
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th>Jam Main:</th>
                        <td>
                            <strong class="mf-highlight-text">
                                <?php echo date('H:i', strtotime($booking->jam_mulai)); ?> - 
                                <?php echo date('H:i', strtotime($booking->jam_selesai)); ?> WIB
                            </strong>
                            <span class="mf-duration-badge">
                                (Durasi: 1 jam)
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Total Pembayaran:</th>
                        <td>
                            <span class="mf-total-price">
                                Rp <?php echo number_format($booking->total_harga, 0, ',', '.'); ?>
                            </span>
                            <br>
                            <small class="mf-payment-note">* Pembayaran di tempat saat datang</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        <div class="mf-card postbox">
            <div class="postbox-header">
                <h2 class="hndle">
                    <span class="dashicons dashicons-info"></span>
                    Informasi Tambahan
                </h2>
            </div>
            <div class="inside">
                <table class="mf-detail-table">
                    <tr>
                        <th>Booking ID:</th>
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
                                echo '<span class="mf-status-badge mf-status-completed">SELESAI</span>';
                            } elseif ($booking_date == $today) {
                                echo '<span class="mf-status-badge mf-status-today">HARI INI</span>';
                            } else {
                                echo '<span class="mf-status-badge mf-status-upcoming">AKAN DATANG</span>';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mf-action-buttons">
            <a href="?page=mari-futsal-booking" class="mf-btn mf-btn-primary mf-btn-large">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
                Kembali ke List Booking
            </a>
        </div>

    </div>
</div>

<style>
/* ========================================
   DETAIL BOOKING STYLES
======================================== */

.mf-detail-booking {
    background: #f5f5f5;
}

.mf-detail-booking .wp-heading-inline {
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    margin-bottom: 10px;
}

.mf-detail-container {
    max-width: 900px;
    margin: 20px auto;
    padding: 0 20px;
}

/* Booking Code Card */
.mf-booking-code-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 30px;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 25px;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.mf-booking-label {
    margin: 0;
    font-size: 14px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.mf-booking-code {
    margin: 15px 0;
    font-size: 42px;
    letter-spacing: 3px;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.mf-booking-timestamp {
    margin: 0;
    font-size: 13px;
    opacity: 0.85;
}

/* Info Grid */
.mf-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 20px;
}

/* Detail Table */
.mf-detail-table {
    width: 100%;
    border-collapse: collapse;
}

.mf-detail-table th {
    width: 40%;
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
    color: #1d2327;
    background: #f6f7f7;
    border-bottom: 1px solid #e0e0e0;
}

.mf-detail-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e0e0e0;
}

.mf-detail-table tr:last-child th,
.mf-detail-table tr:last-child td {
    border-bottom: none;
}

/* Highlight Text */
.mf-highlight-text {
    font-size: 18px;
    color: #2271b1;
}

.mf-duration-badge {
    color: #666;
    margin-left: 10px;
    font-size: 14px;
}

.mf-total-price {
    font-size: 28px;
    font-weight: 700;
    color: #00a32a;
    display: inline-block;
    margin-bottom: 5px;
}

.mf-payment-note {
    color: #666;
    font-style: italic;
}

/* Status Badges */
.mf-status-badge {
    display: inline-block;
    padding: 6px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.mf-status-completed {
    background: #ddd;
    color: #666;
}

.mf-status-today {
    background: #00a32a;
    color: white;
}

.mf-status-upcoming {
    background: #2271b1;
    color: white;
}

/* Action Buttons */
.mf-action-buttons {
    margin-top: 25px;
    text-align: center;
}

.mf-btn-large {
    padding: 12px 24px;
    font-size: 15px;
}

/* ========================================
   RESPONSIVE DESIGN
======================================== */

/* Tablet (768px - 1023px) */
@media screen and (max-width: 1023px) {
    .mf-detail-container {
        max-width: 100%;
        padding: 0 15px;
    }
    
    .mf-booking-code {
        font-size: 36px;
    }
}

/* Mobile L (425px - 767px) */
@media screen and (max-width: 767px) {
    .mf-info-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .mf-booking-code-card {
        padding: 30px 20px;
    }
    
    .mf-booking-code {
        font-size: 28px;
        letter-spacing: 2px;
    }
    
    .mf-detail-table th,
    .mf-detail-table td {
        display: block;
        width: 100%;
        padding: 10px 15px;
    }
    
    .mf-detail-table th {
        background: transparent;
        border-bottom: none;
        padding-bottom: 5px;
        font-size: 12px;
        text-transform: uppercase;
        color: #666;
    }
    
    .mf-detail-table td {
        padding-top: 0;
    }
    
    .mf-detail-table tr {
        display: block;
        margin-bottom: 15px;
    }
    
    .mf-highlight-text {
        font-size: 16px;
    }
    
    .mf-total-price {
        font-size: 24px;
    }
    
    .mf-btn-large {
        width: 100%;
        justify-content: center;
    }
}

/* Mobile M (375px - 424px) */
@media screen and (max-width: 424px) {
    .mf-booking-code {
        font-size: 24px;
    }
    
    .mf-detail-container {
        padding: 0 10px;
    }
    
    .mf-total-price {
        font-size: 20px;
    }
}

/* Mobile S (< 375px) */
@media screen and (max-width: 374px) {
    .mf-booking-code-card {
        padding: 20px 15px;
    }
    
    .mf-booking-code {
        font-size: 20px;
        letter-spacing: 1px;
    }
    
    .mf-booking-label,
    .mf-booking-timestamp {
        font-size: 11px;
    }
}
</style>

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