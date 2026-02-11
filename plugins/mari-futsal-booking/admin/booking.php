<?php
/**
 * Kelola Booking Page
 * List semua booking dengan filter (enhanced - Day 2)
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

MF_Functions::display_flash_message();

// Get filter (future implementation)
$filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : 'all';

// Get bookings
$bookings = MF_Functions::get_all_bookings();
$total_bookings = count($bookings);
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-list-view"></span>
        Kelola Booking
    </h1>
    <hr class="wp-header-end">
    
    <div class="mf-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">
                Semua Booking (<?php echo $total_bookings; ?> Total)
            </h3>
            
            <!-- Filter Buttons (Day 2 - Basic) -->
            <div class="mf-filter-buttons">
                <a href="<?php echo admin_url('admin.php?page=mari-futsal-booking&filter=all'); ?>" 
                   class="mf-btn mf-btn-small <?php echo $filter === 'all' ? 'mf-btn-primary' : 'mf-btn-secondary'; ?>">
                    <span class="dashicons dashicons-menu"></span>
                    Semua
                </a>
                <a href="<?php echo admin_url('admin.php?page=mari-futsal-booking&filter=today'); ?>" 
                   class="mf-btn mf-btn-small <?php echo $filter === 'today' ? 'mf-btn-primary' : 'mf-btn-secondary'; ?>" 
                   title="Day 6: Filter by today">
                    <span class="dashicons dashicons-calendar"></span>
                    Hari Ini
                </a>
                <a href="<?php echo admin_url('admin.php?page=mari-futsal-booking&filter=week'); ?>" 
                   class="mf-btn mf-btn-small <?php echo $filter === 'week' ? 'mf-btn-primary' : 'mf-btn-secondary'; ?>" 
                   title="Day 6: Filter by week">
                    <span class="dashicons dashicons-calendar-alt"></span>
                    Minggu Ini
                </a>
            </div>
        </div>
        
        <div style="background: #d4edda; padding: 15px; border-radius: 4px; border-left: 4px solid #155724; margin-bottom: 20px;">
            <p style="margin: 0; color: #155724;">
                <span class="dashicons dashicons-info"></span>
                <strong>Day 2:</strong> Filter buttons ditambahkan. 
                <strong>Day 6:</strong> Filter functionality & detail view akan diimplementasikan.
            </p>
        </div>
        
        <?php if ($bookings): ?>
        <div class="mf-table-wrapper">
            <table class="wp-list-table widefat fixed striped mf-table">
                <thead>
                    <tr>
                        <th style="width: 12%;">Kode Booking</th>
                        <th style="width: 13%;">Tanggal</th>
                        <th style="width: 18%;">Lapangan</th>
                        <th style="width: 12%;">Jam</th>
                        <th style="width: 16%;">Customer</th>
                        <th style="width: 12%;">No HP</th>
                        <th style="width: 12%;">Total</th>
                        <th style="width: 5%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><code><?php echo esc_html($booking->kode_booking); ?></code></td>
                        <td><?php echo MF_Functions::format_date($booking->tanggal); ?></td>
                        <td><?php echo esc_html($booking->nama_lapangan); ?></td>
                        <td>
                            <span class="dashicons dashicons-clock" style="font-size: 14px;"></span>
                            <?php echo MF_Functions::format_time($booking->jam_mulai) . ' - ' . MF_Functions::format_time($booking->jam_selesai); ?>
                        </td>
                        <td><?php echo esc_html($booking->nama); ?></td>
                        <td><?php echo esc_html($booking->no_hp); ?></td>
                        <td><strong><?php echo MF_Functions::format_rupiah($booking->total_harga); ?></strong></td>
                        <td>
                            <button class="mf-btn mf-btn-small mf-btn-danger mf-btn-disabled" disabled title="Day 6: Delete booking">
                                <span class="dashicons dashicons-trash"></span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="mf-empty-state">
            <span class="dashicons dashicons-list-view" style="font-size: 48px; color: #ddd;"></span>
            <p>Belum ada booking.</p>
            <p><small>Booking dari user akan muncul di sini.</small></p>
        </div>
        <?php endif; ?>
    </div>
</div>