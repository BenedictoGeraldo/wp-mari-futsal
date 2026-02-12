<?php
/**
 * Kelola Jadwal Page - Full CRUD (Day 4)
 * Master slot waktu dengan fitur Add, Edit, Delete
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

// ========================================
// HANDLE POST REQUESTS (Add, Edit, Delete)
// ========================================

// ADD JADWAL
if (isset($_POST['action']) && $_POST['action'] === 'add_jadwal') {
    if (!MF_Functions::verify_nonce('add_jadwal_action')) {
        MF_Functions::set_flash_message('❌ Security check gagal! Silakan coba lagi.', 'error');
    } else {
        $data = array(
            'jam_mulai' => sanitize_text_field($_POST['jam_mulai']),
            'jam_selesai' => sanitize_text_field($_POST['jam_selesai'])
        );
        
        $errors = MF_Functions::validate_jadwal_data($data, 'add');
        
        if (!empty($errors)) {
            $error_msg = '❌ Gagal menambahkan slot waktu:<br><ul style="margin: 10px 0; padding-left: 20px;">';
            foreach ($errors as $error) {
                $error_msg .= '<li>' . $error . '</li>';
            }
            $error_msg .= '</ul>';
            MF_Functions::set_flash_message($error_msg, 'error');
        } else {
            $result = MF_Functions::add_jadwal($data);
            
            if ($result) {
                MF_Functions::set_flash_message('✅ Slot waktu berhasil ditambahkan!', 'success');
                echo '<script>window.location.href = "' . admin_url('admin.php?page=mari-futsal-jadwal') . '";</script>';
                exit;
            } else {
                MF_Functions::set_flash_message('❌ Gagal menambahkan slot waktu ke database.', 'error');
            }
        }
    }
}

// EDIT JADWAL
if (isset($_POST['action']) && $_POST['action'] === 'edit_jadwal') {
    if (!MF_Functions::verify_nonce('edit_jadwal_action')) {
        MF_Functions::set_flash_message('❌ Security check gagal! Silakan coba lagi.', 'error');
    } else {
        $jadwal_id = intval($_POST['jadwal_id']);
        
        $data = array(
            'jam_mulai' => sanitize_text_field($_POST['jam_mulai']),
            'jam_selesai' => sanitize_text_field($_POST['jam_selesai'])
        );
        
        $errors = MF_Functions::validate_jadwal_data($data, 'edit', $jadwal_id);
        
        if (!empty($errors)) {
            $error_msg = '❌ Gagal mengupdate slot waktu:<br><ul style="margin: 10px 0; padding-left: 20px;">';
            foreach ($errors as $error) {
                $error_msg .= '<li>' . $error . '</li>';
            }
            $error_msg .= '</ul>';
            MF_Functions::set_flash_message($error_msg, 'error');
        } else {
            $result = MF_Functions::update_jadwal($jadwal_id, $data);
            
            if ($result !== false) {
                MF_Functions::set_flash_message('✅ Slot waktu berhasil diupdate!', 'success');
                echo '<script>window.location.href = "' . admin_url('admin.php?page=mari-futsal-jadwal') . '";</script>';
                exit;
            } else {
                MF_Functions::set_flash_message('❌ Gagal mengupdate slot waktu ke database.', 'error');
            }
        }
    }
}

// DELETE JADWAL
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!MF_Functions::verify_nonce('delete_jadwal')) {
        MF_Functions::set_flash_message('❌ Security check gagal! Link tidak valid atau sudah expired.', 'error');
    } else {
        $jadwal_id = intval($_GET['id']);
        
        // Check if jadwal is being used in bookings
        $booking_count = MF_Functions::get_booking_count_by_jadwal($jadwal_id);
        
        if ($booking_count > 0) {
            MF_Functions::set_flash_message(
                "❌ Tidak bisa menghapus! Slot waktu ini sedang digunakan pada <strong>{$booking_count} booking aktif</strong>.<br>Hapus booking terlebih dahulu.", 
                'error'
            );
        } else {
            $result = MF_Functions::delete_jadwal($jadwal_id);
            
            if ($result) {
                MF_Functions::set_flash_message('✅ Slot waktu berhasil dihapus!', 'success');
            } else {
                MF_Functions::set_flash_message('❌ Gagal menghapus slot waktu dari database.', 'error');
            }
        }
    }
    
    // Use JavaScript redirect to avoid header errors
    echo '<script>window.location.href = "' . admin_url('admin.php?page=mari-futsal-jadwal') . '";</script>';
    exit;
}

// ========================================
// GET DATA FOR DISPLAY
// ========================================

MF_Functions::display_flash_message();

$jadwal_list = MF_Functions::get_all_jadwal();
$action = isset($_GET['form']) ? $_GET['form'] : '';
$edit_jadwal = null;

if ($action === 'edit' && isset($_GET['id'])) {
    $edit_jadwal = MF_Functions::get_jadwal(intval($_GET['id']));
    if (!$edit_jadwal) {
        echo '<script>window.location.href = "' . admin_url('admin.php?page=mari-futsal-jadwal') . '";</script>';
        exit;
    }
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-clock"></span>
        Kelola Jadwal (Master Slot Waktu)
    </h1>
    <a href="?page=mari-futsal-jadwal&form=add" class="page-title-action">
        <span class="dashicons dashicons-plus-alt"></span>
        Tambah Slot Waktu
    </a>
    <hr class="wp-header-end">
    
    <?php if ($action === 'add' || $action === 'edit'): ?>
        <!-- FORM ADD/EDIT -->
        <div class="mf-card" style="max-width: 600px;">
            <h2><?php echo $action === 'add' ? '➕ Tambah Slot Waktu Baru' : '✏️ Edit Slot Waktu'; ?></h2>
            
            <form method="post" action="" class="mf-form">
                <?php 
                wp_nonce_field($action === 'add' ? 'add_jadwal_action' : 'edit_jadwal_action', '_wpnonce');
                ?>
                <input type="hidden" name="action" value="<?php echo $action === 'add' ? 'add_jadwal' : 'edit_jadwal'; ?>">
                <?php if ($action === 'edit'): ?>
                    <input type="hidden" name="jadwal_id" value="<?php echo $edit_jadwal->id; ?>">
                <?php endif; ?>
                
                <table class="form-table">
                    <tr>
                        <th><label for="jam_mulai">Jam Mulai *</label></th>
                        <td>
                            <input 
                                type="time" 
                                name="jam_mulai" 
                                id="jam_mulai" 
                                class="regular-text" 
                                value="<?php echo $action === 'edit' ? date('H:i', strtotime($edit_jadwal->jam_mulai)) : '08:00'; ?>"
                                required
                            >
                            <p class="description">Format 24 jam (contoh: 08:00 atau 20:30)</p>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="jam_selesai">Jam Selesai *</label></th>
                        <td>
                            <input 
                                type="time" 
                                name="jam_selesai" 
                                id="jam_selesai" 
                                class="regular-text" 
                                value="<?php echo $action === 'edit' ? date('H:i', strtotime($edit_jadwal->jam_selesai)) : '09:00'; ?>"
                                required
                            >
                            <p class="description">Harus lebih besar dari jam mulai</p>
                        </td>
                    </tr>
                </table>
                
                <div class="mf-form-actions">
                    <button type="submit" class="button button-primary button-large">
                        <span class="dashicons dashicons-<?php echo $action === 'add' ? 'plus-alt' : 'edit'; ?>"></span>
                        <?php echo $action === 'add' ? 'Tambah Slot' : 'Update Slot'; ?>
                    </button>
                    <a href="?page=mari-futsal-jadwal" class="button button-secondary button-large">
                        <span class="dashicons dashicons-no-alt"></span>
                        Batal
                    </a>
                </div>
            </form>
        </div>
        
    <?php else: ?>
        <!-- LIST JADWAL -->
        <div class="mf-card">
            <div style="background: #d4edda; padding: 15px; border-radius: 4px; border-left: 4px solid #28a745; margin-bottom: 20px;">
                <h3 style="margin: 0 0 10px 0; color: #155724;">
                    <span class="dashicons dashicons-yes-alt"></span>
                    Day 4 - CRUD Jadwal Complete!
                </h3>
                <p style="margin: 0; color: #155724;">
                    ✅ Tambah slot waktu | ✅ Edit slot | ✅ Hapus slot (with validation)
                </p>
            </div>
            
            <h3>📋 Slot Waktu Tersedia (<?php echo count($jadwal_list); ?> Slot)</h3>
            
            <?php if ($jadwal_list): ?>
            <div class="mf-table-wrapper">
                <table class="wp-list-table widefat fixed striped mf-table">
                    <thead>
                        <tr>
                            <th style="width: 10%;">ID</th>
                            <th style="width: 25%;">Jam Mulai</th>
                            <th style="width: 25%;">Jam Selesai</th>
                            <th style="width: 20%;">Durasi</th>
                            <th style="width: 20%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jadwal_list as $jadwal): 
                            $durasi = MF_Functions::calculate_slot_duration($jadwal->jam_mulai, $jadwal->jam_selesai);
                        ?>
                        <tr>
                            <td><strong>#<?php echo $jadwal->id; ?></strong></td>
                            <td>
                                <span class="dashicons dashicons-clock" style="color: #0071a1;"></span>
                                <?php echo date('H:i', strtotime($jadwal->jam_mulai)); ?>
                            </td>
                            <td>
                                <span class="dashicons dashicons-clock" style="color: #dc3545;"></span>
                                <?php echo date('H:i', strtotime($jadwal->jam_selesai)); ?>
                            </td>
                            <td>
                                <span class="mf-badge" style="background: #10b981; color: #fff;">
                                    <?php echo $durasi; ?> menit
                                </span>
                            </td>
                            <td>
                                <a 
                                    href="?page=mari-futsal-jadwal&form=edit&id=<?php echo $jadwal->id; ?>" 
                                    class="button button-small"
                                    title="Edit"
                                >
                                    <span class="dashicons dashicons-edit"></span>
                                    Edit
                                </a>
                                <a 
                                    href="<?php echo wp_nonce_url(
                                        admin_url('admin.php?page=mari-futsal-jadwal&action=delete&id=' . $jadwal->id),
                                        'delete_jadwal'
                                    ); ?>" 
                                    class="button button-small button-link-delete"
                                    onclick="return confirm('Yakin ingin menghapus slot waktu ini?\n\n⚠️ Tidak bisa dihapus jika ada booking yang menggunakan slot ini.');"
                                    style="color: #dc3545;"
                                >
                                    <span class="dashicons dashicons-trash"></span>
                                    Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="mf-empty-state">
                <span class="dashicons dashicons-clock" style="font-size: 64px; opacity: 0.3;"></span>
                <h3>Belum ada slot waktu</h3>
                <p>Klik tombol "Tambah Slot Waktu" atau "Bulk Create" untuk memulai.</p>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>