<?php
/**
 * Kelola Jadwal Page
 * CRUD untuk master slot waktu (akan dikembangkan Day 4)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Security check
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

$jadwal_list = MF_Functions::get_all_jadwal();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Kelola Jadwal (Master Slot Waktu)</h1>
    <a href="#" class="page-title-action">Tambah Slot Baru</a>
    <hr class="wp-header-end">
    
    <div style="background: #fff; margin-top: 20px; padding: 20px;">
        <p><strong>📅 Status:</strong> Halaman ini akan dikembangkan di <strong>Day 4</strong></p>
        <p>Fitur yang akan tersedia:</p>
        <ul>
            <li>Tabel list semua slot waktu</li>
            <li>Form tambah slot waktu (jam mulai, jam selesai)</li>
            <li>Form edit slot</li>
            <li>Hapus slot dengan validasi</li>
            <li>Bulk create slots (auto-generate)</li>
        </ul>
        
        <h3>Preview Master Slot Waktu Saat Ini:</h3>
        <?php if ($jadwal_list): ?>
        <table class="wp-list-table widefat fixed striped" style="max-width: 600px;">
            <thead>
                <tr>
                    <th style="width: 20%;">ID</th>
                    <th style="width: 40%;">Jam Mulai</th>
                    <th style="width: 40%;">Jam Selesai</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($jadwal_list as $jadwal): ?>
                <tr>
                    <td><?php echo $jadwal->id; ?></td>
                    <td><strong><?php echo MF_Functions::format_time($jadwal->jam_mulai); ?></strong></td>
                    <td><strong><?php echo MF_Functions::format_time($jadwal->jam_selesai); ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><em>Total: <?php echo count($jadwal_list); ?> slot waktu tersedia</em></p>
        <?php else: ?>
        <p>Belum ada slot waktu.</p>
        <?php endif; ?>
    </div>
</div>