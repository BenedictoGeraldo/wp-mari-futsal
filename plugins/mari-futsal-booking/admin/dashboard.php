<?php
/**
 * Dashboard Page - Admin Overview
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

MF_Functions::display_flash_message();

$total_lapangan = MF_Functions::get_total_lapangan();
$total_jadwal = MF_Functions::get_total_jadwal();
$booking_today = MF_Functions::get_booking_count_today();
$booking_week = MF_Functions::get_booking_count_week();
$recent_bookings = MF_Functions::get_all_bookings(10);
$today = date('Y-m-d');
$bookings_today = MF_Functions::get_bookings_by_date($today);
?>

<div class="wrap mf-dashboard">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-chart-area" style="font-size: 28px; vertical-align: middle;"></span>
        Dashboard Mari Futsal
    </h1>
    <hr class="wp-header-end">
    
    <!-- Welcome Card -->
    <div class="mf-card mf-welcome-card">
        <h2>👋 Selamat Datang di Mari Futsal Booking System!</h2>
        <p style="font-size: 15px; line-height: 1.6;">
            Dashboard ini menampilkan statistik dan data booking secara real-time. 
            Kelola lapangan, jadwal, dan booking pelanggan Anda dengan mudah.
        </p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="mf-stats-grid">
        <div class="mf-stat-card" style="border-left-color: #2271b1;">
            <div class="mf-stat-icon" style="color: #2271b1;">
                <span class="dashicons dashicons-admin-multisite" style="font-size: 40px;"></span>
            </div>
            <div class="mf-stat-content">
                <h3>Total Lapangan</h3>
                <p class="value" style="color: #2271b1;"><?php echo $total_lapangan; ?></p>
                <small style="color: #666;">Lapangan aktif</small>
            </div>
        </div>
        
        <div class="mf-stat-card" style="border-left-color: #dba617;">
            <div class="mf-stat-icon" style="color: #dba617;">
                <span class="dashicons dashicons-clock" style="font-size: 40px;"></span>
            </div>
            <div class="mf-stat-content">
                <h3>Total Slot Waktu</h3>
                <p class="value" style="color: #dba617;"><?php echo $total_jadwal; ?></p>
                <small style="color: #666;">Slot tersedia</small>
            </div>
        </div>
        
        <div class="mf-stat-card" style="border-left-color: #00a32a;">
            <div class="mf-stat-icon" style="color: #00a32a;">
                <span class="dashicons dashicons-calendar-alt" style="font-size: 40px;"></span>
            </div>
            <div class="mf-stat-content">
                <h3>Booking Hari Ini</h3>
                <p class="value" style="color: #00a32a;"><?php echo $booking_today; ?></p>
                <small style="color: #666;"><?php echo MF_Functions::format_date($today); ?></small>
            </div>
        </div>
        
        <div class="mf-stat-card" style="border-left-color: #d63638;">
            <div class="mf-stat-icon" style="color: #d63638;">
                <span class="dashicons dashicons-chart-line" style="font-size: 40px;"></span>
            </div>
            <div class="mf-stat-content">
                <h3>Booking Minggu Ini</h3>
                <p class="value" style="color: #d63638;"><?php echo $booking_week; ?></p>
                <small style="color: #666;">Total booking minggu ini</small>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="mf-card" style="display:flex; flex-direction:column; justify-content:center; align-items: center;">
        <h2 style="margin-top: 0;">
            <span class="dashicons dashicons-admin-tools"></span>
            Quick Actions
        </h2>
        <div class="mf-quick-actions" style="display: flex;">
            <a href="<?php echo admin_url('admin.php?page=mari-futsal-lapangan'); ?>" class="mf-btn mf-btn-primary">
                <span class="dashicons dashicons-plus-alt"></span>
                Kelola Lapangan
            </a>
            <a href="<?php echo admin_url('admin.php?page=mari-futsal-jadwal'); ?>" class="mf-btn mf-btn-secondary">
                <span class="dashicons dashicons-clock"></span>
                Kelola Jadwal
            </a>
            <a href="<?php echo admin_url('admin.php?page=mari-futsal-booking'); ?>" class="mf-btn mf-btn-success">
                <span class="dashicons dashicons-list-view"></span>
                Lihat Semua Booking
            </a>
        </div>
    </div>
</div>