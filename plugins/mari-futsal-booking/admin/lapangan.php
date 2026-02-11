<?php
/**
 * Kelola Lapangan Page
 * Preview with action buttons (CRUD ready - Day 3)
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

MF_Functions::display_flash_message();

$lapangan_list = MF_Functions::get_all_lapangan(null);
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-admin-multisite"></span>
        Kelola Lapangan
    </h1>
    <a href="#" class="page-title-action mf-btn-disabled" style="pointer-events: none; opacity: 0.5;">
        <span class="dashicons dashicons-plus-alt"></span>
        Tambah Lapangan
    </a>
    <hr class="wp-header-end">
    
    <div class="mf-card">
        <div style="background: #fff3cd; padding: 15px; border-radius: 4px; border-left: 4px solid #dba617; margin-bottom: 20px;">
            <h3 style="margin: 0 0 10px 0; color: #856404;">
                <span class="dashicons dashicons-info"></span>
                Status Development
            </h3>
            <p style="margin: 0; color: #856404;">
                <strong>Day 2:</strong> Action buttons sudah ditambahkan (disabled). 
                <strong>Day 3:</strong> CRUD akan diimplementasikan (Add, Edit, Delete dengan form & validation).
            </p>
        </div>
        
        <h3>Data Lapangan</h3>
        
        <?php if ($lapangan_list): ?>
        <div class="mf-table-wrapper">
            <table class="wp-list-table widefat fixed striped mf-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 25%;">Nama Lapangan</th>
                        <th style="width: 20%;">Jenis</th>
                        <th style="width: 17%;">Harga / Jam</th>
                        <th style="width: 12%;">Status</th>
                        <th style="width: 18%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lapangan_list as $lap): ?>
                    <tr>
                        <td><?php echo $lap->id; ?></td>
                        <td>
                            <strong><?php echo esc_html($lap->nama); ?></strong>
                        </td>
                        <td><?php echo esc_html($lap->jenis_lapangan); ?></td>
                        <td><strong><?php echo MF_Functions::format_rupiah($lap->harga); ?></strong></td>
                        <td>
                            <?php if ($lap->status === 'aktif'): ?>
                                <span class="mf-badge mf-badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="mf-badge mf-badge-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="mf-btn mf-btn-small mf-btn-primary mf-btn-disabled" disabled title="Available in Day 3">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </button>
                            <button class="mf-btn mf-btn-small mf-btn-danger mf-btn-disabled" disabled title="Available in Day 3">
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
            <span class="dashicons dashicons-admin-multisite" style="font-size: 48px; color: #ddd;"></span>
            <p>Belum ada data lapangan.</p>
            <p><small>Klik "Tambah Lapangan" untuk menambah data baru.</small></p>
        </div>
        <?php endif; ?>
    </div>
</div>