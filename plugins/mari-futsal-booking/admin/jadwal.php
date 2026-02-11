<?php
/**
 * Kelola Jadwal Page
 * Master slot waktu with action buttons (CRUD ready - Day 4)
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

MF_Functions::display_flash_message();

$jadwal_list = MF_Functions::get_all_jadwal();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-clock"></span>
        Kelola Jadwal (Master Slot Waktu)
    </h1>
    <a href="#" class="page-title-action mf-btn-disabled" style="pointer-events: none; opacity: 0.5;">
        <span class="dashicons dashicons-plus-alt"></span>
        Tambah Slot Waktu
    </a>
    <hr class="wp-header-end">
    
    <div class="mf-card">
        <div style="background: #d1ecf1; padding: 15px; border-radius: 4px; border-left: 4px solid #0c5460; margin-bottom: 20px;">
            <h3 style="margin: 0 0 10px 0; color: #0c5460;">
                <span class="dashicons dashicons-info"></span>
                Status Development
            </h3>
            <p style="margin: 0; color: #0c5460;">
                <strong>Day 2:</strong> Action buttons sudah ditambahkan (disabled). 
                <strong>Day 4:</strong> CRUD Jadwal akan diimplementasikan (Add, Edit, Delete slot waktu).
            </p>
        </div>
        
        <h3>Slot Waktu Tersedia (<?php echo count($jadwal_list); ?> Slot)</h3>
        
        <?php if ($jadwal_list): ?>
        <div class="mf-table-wrapper">
            <table class="wp-list-table widefat fixed striped mf-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">ID</th>
                        <th style="width: 30%;">Jam Mulai</th>
                        <th style="width: 30%;">Jam Selesai</th>
                        <th style="width: 25%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jadwal_list as $jadwal): ?>
                    <tr>
                        <td><?php echo $jadwal->id; ?></td>
                        <td>
                            <span class="dashicons dashicons-clock"></span>
                            <strong><?php echo MF_Functions::format_time($jadwal->jam_mulai); ?></strong>
                        </td>
                        <td>
                            <span class="dashicons dashicons-clock"></span>
                            <strong><?php echo MF_Functions::format_time($jadwal->jam_selesai); ?></strong>
                        </td>
                        <td>
                            <button class="mf-btn mf-btn-small mf-btn-primary mf-btn-disabled" disabled title="Available in Day 4">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button class="mf-btn mf-btn-small mf-btn-danger mf-btn-disabled" disabled title="Available in Day 4">
                                <span class="dashicons dashicons-trash"></span>
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="mf-empty-state">
            <span class="dashicons dashicons-clock" style="font-size: 48px; color: #ddd;"></span>
            <p>Belum ada slot waktu.</p>
            <p><small>Tambahkan slot waktu untuk sistem booking.</small></p>
        </div>
        <?php endif; ?>
    </div>
</div>