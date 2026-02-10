<?php
/**
 * Kelola Lapangan Page
 * CRUD untuk data lapangan (akan dikembangkan Day 3)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

$lapangan_list = MF_Functions::get_all_lapangan(null); // Get all including nonaktif
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Kelola Lapangan</h1>
    <a href="#" class="page-title-action">Tambah Lapangan Baru</a>
    <hr class="wp-header-end">
    
    <div style="background: #fff; margin-top: 20px; padding: 20px;">
        <p><strong>📅 Status:</strong> Halaman ini akan dikembangkan di <strong>Day 3</strong></p>
        <p>Fitur yang akan tersedia:</p>
        <ul>
            <li>Tabel list semua lapangan</li>
            <li>Form tambah lapangan (nama, jenis, harga, status, foto)</li>
            <li>Form edit lapangan</li>
            <li>Hapus lapangan dengan konfirmasi</li>
            <li>Upload & preview foto lapangan</li>
        </ul>
        
        <h3>Preview Data Lapangan Saat Ini:</h3>
        <?php if ($lapangan_list): ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 25%;">Nama</th>
                    <th style="width: 25%;">Jenis</th>
                    <th style="width: 20%;">Harga</th>
                    <th style="width: 20%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lapangan_list as $lap): ?>
                <tr>
                    <td><?php echo $lap->id; ?></td>
                    <td><strong><?php echo esc_html($lap->nama); ?></strong></td>
                    <td><?php echo esc_html($lap->jenis_lapangan); ?></td>
                    <td><?php echo MF_Functions::format_rupiah($lap->harga); ?></td>
                    <td>
                        <span style="padding: 3px 8px; border-radius: 3px; font-size: 12px; <?php echo $lap->status === 'aktif' ? 'background: #00a32a; color: #fff;' : 'background: #ddd; color: #666;'; ?>">
                            <?php echo ucfirst($lap->status); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>Belum ada data lapangan.</p>
        <?php endif; ?>
    </div>
</div>