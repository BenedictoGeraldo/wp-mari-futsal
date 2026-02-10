<?php
/**
 * Kelola Booking Page
 * View & manage semua booking (akan dikembangkan Day 6)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

$all_bookings = MF_Functions::get_all_bookings(10); // Get latest 10
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Kelola Booking</h1>
    <hr class="wp-header-end">
    
    <div style="background: #fff; margin-top: 20px; padding: 20px;">
        <p><strong>📅 Status:</strong> Halaman ini akan dikembangkan di <strong>Day 6</strong></p>
        <p>Fitur yang akan tersedia:</p>
        <ul>
            <li>Tabel list semua booking</li>
            <li>Filter by date range, lapangan</li>
            <li>Search by kode booking, nama customer</li>
            <li>View detail booking</li>
            <li>Cancel/delete booking</li>
            <li>Export booking data</li>
        </ul>
        
        <?php if ($all_bookings): ?>
        <h3>Preview Latest Bookings:</h3>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 15%;">Kode</th>
                    <th style="width: 15%;">Tanggal</th>
                    <th style="width: 20%;">Lapangan</th>
                    <th style="width: 15%;">Jam</th>
                    <th style="width: 20%;">Customer</th>
                    <th style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_bookings as $booking): ?>
                <tr>
                    <td><strong><?php echo esc_html($booking->kode_booking); ?></strong></td>
                    <td><?php echo MF_Functions::format_date($booking->tanggal); ?></td>
                    <td><?php echo esc_html($booking->nama_lapangan); ?></td>
                    <td><?php echo MF_Functions::format_time($booking->jam_mulai); ?></td>
                    <td><?php echo esc_html($booking->nama); ?></td>
                    <td><?php echo MF_Functions::format_rupiah($booking->total_harga); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p style="text-align: center; padding: 40px; color: #666;">Belum ada booking.</p>
        <?php endif; ?>
    </div>
</div>