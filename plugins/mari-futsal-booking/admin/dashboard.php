<?php
/**
 * Dashboard Page
 * Tampilan dashboard dengan cards statistik & tabel booking hari ini
 */

if (!defined('ABSPATH')) {
    exit;
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

// Get statistics
$total_lapangan = MF_Functions::get_total_lapangan();
$total_jadwal = MF_Functions::get_total_jadwal();
$booking_today = MF_Functions::get_booking_count_today();
$booking_week = MF_Functions::get_booking_count_week();

// Get today's bookings
$today = date('Y-m-d');
$bookings_today = MF_Functions::get_bookings_by_date($today);
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Dashboard Mari Futsal</h1>
    <hr class="wp-header-end">
    
    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
        
        <!-- Card: Total Lapangan -->
        <div style="background: #fff; padding: 20px; border-left: 4px solid #2271b1; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Total Lapangan</h3>
            <p style="margin: 0; font-size: 32px; font-weight: bold; color: #2271b1;"><?php echo $total_lapangan; ?></p>
        </div>
        
        <!-- Card: Total Slot Waktu -->
        <div style="background: #fff; padding: 20px; border-left: 4px solid #dba617; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Total Slot Waktu</h3>
            <p style="margin: 0; font-size: 32px; font-weight: bold; color: #dba617;"><?php echo $total_jadwal; ?></p>
        </div>
        
        <!-- Card: Booking Hari Ini -->
        <div style="background: #fff; padding: 20px; border-left: 4px solid #00a32a; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Booking Hari Ini</h3>
            <p style="margin: 0; font-size: 32px; font-weight: bold; color: #00a32a;"><?php echo $booking_today; ?></p>
        </div>
        
        <!-- Card: Booking Minggu Ini -->
        <div style="background: #fff; padding: 20px; border-left: 4px solid #d63638; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="margin: 0 0 10px 0; color: #666; font-size: 14px;">Booking Minggu Ini</h3>
            <p style="margin: 0; font-size: 32px; font-weight: bold; color: #d63638;"><?php echo $booking_week; ?></p>
        </div>
        
    </div>
    
    <!-- Today's Bookings -->
    <div style="margin-top: 30px; background: #fff; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Booking Hari Ini (<?php echo MF_Functions::format_date($today); ?>)</h2>
        
        <?php if (empty($bookings_today)): ?>
            <p style="color: #666; text-align: center; padding: 40px;">Belum ada booking hari ini.</p>
        <?php else: ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th style="width: 15%;">Kode Booking</th>
                        <th style="width: 20%;">Lapangan</th>
                        <th style="width: 15%;">Jam</th>
                        <th style="width: 20%;">Nama Customer</th>
                        <th style="width: 15%;">No HP</th>
                        <th style="width: 15%;">Total Harga</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings_today as $booking): ?>
                    <tr>
                        <td><strong><?php echo esc_html($booking->kode_booking); ?></strong></td>
                        <td><?php echo esc_html($booking->nama_lapangan); ?></td>
                        <td><?php echo MF_Functions::format_time($booking->jam_mulai) . ' - ' . MF_Functions::format_time($booking->jam_selesai); ?></td>
                        <td><?php echo esc_html($booking->nama); ?></td>
                        <td><?php echo esc_html($booking->no_hp); ?></td>
                        <td><strong><?php echo MF_Functions::format_rupiah($booking->total_harga); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Welcome Message -->
    <div style="margin-top: 30px; background: #fff; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2>Selamat Datang di Mari Futsal Booking System! 🎉</h2>
        <p>Dashboard ini menampilkan statistik dan data booking secara real-time.</p>
        <p><strong>Status Development:</strong> ✅ Day 1 - Database & Plugin Structure Complete</p>
        <ul style="line-height: 1.8;">
            <li>✅ Database tables created (lapangan, jadwal, booking)</li>
            <li>✅ Dummy data inserted (3 lapangan, 14 slot waktu)</li>
            <li>✅ Admin menu registered</li>
            <li>✅ Dashboard dengan statistik berfungsi</li>
            <li>⏳ Next: Day 2 - Enhanced Dashboard & Security</li>
        </ul>
    </div>
</div>