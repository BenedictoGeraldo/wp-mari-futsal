<?php
/**
 * Kelola Lapangan Page - CRUD Management
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

// ADD LAPANGAN
if (isset($_POST['action']) && $_POST['action'] === 'add_lapangan') {
    // Verify nonce
    if (!MF_Functions::verify_nonce('add_lapangan_action')) {
        MF_Functions::set_flash_message('Security check failed!', 'error');
    } else {
        // Get form data
        $data = array(
            'nama' => $_POST['nama'],
            'jenis_lapangan' => $_POST['jenis_lapangan'],
            'harga' => $_POST['harga'],
            'status' => $_POST['status']
        );
        
        // Validate data
        $errors = MF_Functions::validate_lapangan_data($data, 'add');
        
        if (!empty($errors)) {
            MF_Functions::set_flash_message(implode('<br>', $errors), 'error');
        } else {
            // Handle image upload if exists
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload_result = MF_Functions::handle_lapangan_image_upload($_FILES['foto']);
                
                if ($upload_result['success']) {
                    $data['foto'] = $upload_result['filename'];
                } else {
                    MF_Functions::set_flash_message($upload_result['error'], 'error');
                }
            }
            
            // Insert to database
            if (empty($errors)) {
                $result = MF_Functions::add_lapangan($data);
                
                if ($result) {
                    MF_Functions::set_flash_message('Lapangan berhasil ditambahkan!', 'success');
                    // Use JavaScript redirect to avoid header errors
                    echo '<script>window.location.href = "' . admin_url('admin.php?page=mari-futsal-lapangan') . '";</script>';
                    exit;
                } else {
                    MF_Functions::set_flash_message('Gagal menambahkan lapangan.', 'error');
                }
            }
        }
    }
}

// EDIT LAPANGAN
if (isset($_POST['action']) && $_POST['action'] === 'edit_lapangan') {
    if (!MF_Functions::verify_nonce('edit_lapangan_action')) {
        MF_Functions::set_flash_message('Security check failed!', 'error');
    } else {
        $lapangan_id = intval($_POST['lapangan_id']);
        
        $data = array(
            'nama' => $_POST['nama'],
            'jenis_lapangan' => $_POST['jenis_lapangan'],
            'harga' => $_POST['harga'],
            'status' => $_POST['status']
        );
        
        $errors = MF_Functions::validate_lapangan_data($data, 'edit');
        
        if (!empty($errors)) {
            MF_Functions::set_flash_message(implode('<br>', $errors), 'error');
        } else {
            // Handle new image upload
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
                $upload_result = MF_Functions::handle_lapangan_image_upload($_FILES['foto']);
                
                if ($upload_result['success']) {
                    // Delete old image
                    $old_lapangan = MF_Functions::get_lapangan($lapangan_id);
                    if ($old_lapangan && !empty($old_lapangan->foto)) {
                        MF_Functions::delete_lapangan_image($old_lapangan->foto);
                    }
                    
                    $data['foto'] = $upload_result['filename'];
                } else {
                    MF_Functions::set_flash_message($upload_result['error'], 'error');
                }
            }
            
            if (empty($errors)) {
                $result = MF_Functions::update_lapangan($lapangan_id, $data);
                
                if ($result) {
                    MF_Functions::set_flash_message('Lapangan berhasil diupdate!', 'success');
                    // Use JavaScript redirect to avoid header errors
                    echo '<script>window.location.href = "' . admin_url('admin.php?page=mari-futsal-lapangan') . '";</script>';
                    exit;
                } else {
                    MF_Functions::set_flash_message('Gagal mengupdate lapangan.', 'error');
                }
            }
        }
    }
}

// DELETE LAPANGAN
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'delete_lapangan_' . $_GET['id'])) {
        MF_Functions::set_flash_message('Security check failed!', 'error');
    } else {
        $lapangan_id = intval($_GET['id']);
        
        // Check if has bookings
        if (MF_Functions::check_lapangan_has_bookings($lapangan_id)) {
            MF_Functions::set_flash_message('Tidak dapat menghapus lapangan yang masih memiliki booking!', 'error');
        } else {
            $result = MF_Functions::delete_lapangan($lapangan_id);
            
            if ($result) {
                MF_Functions::set_flash_message('Lapangan berhasil dihapus!', 'success');
            } else {
                MF_Functions::set_flash_message('Gagal menghapus lapangan.', 'error');
            }
        }
        
        // Use JavaScript redirect to avoid header errors
        echo '<script>window.location.href = "' . admin_url('admin.php?page=mari-futsal-lapangan') . '";</script>';
        exit;
    }
}

// Display flash messages
MF_Functions::display_flash_message();

// Get data for edit mode
$edit_mode = false;
$edit_data = null;
if (isset($_GET['edit']) && isset($_GET['id'])) {
    $edit_mode = true;
    $edit_data = MF_Functions::get_lapangan(intval($_GET['id']));
}

// Get all lapangan
$lapangan_list = MF_Functions::get_all_lapangan(null);
$upload_dir = wp_upload_dir();
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-admin-multisite"></span>
        Kelola Lapangan
    </h1>
    <button type="button" class="page-title-action" id="btn-toggle-form">
        <span class="dashicons dashicons-plus-alt"></span>
        Tambah Lapangan
    </button>
    <hr class="wp-header-end">
    
    <!-- Form Add/Edit Lapangan (Hidden by default) -->
    <div class="mf-card mf-form-container" id="form-container" style="display: <?php echo $edit_mode ? 'block' : 'none'; ?>;">
        <h2>
            <span class="dashicons dashicons-edit"></span>
            <?php echo $edit_mode ? 'Edit Lapangan' : 'Tambah Lapangan Baru'; ?>
        </h2>
        
        <form method="post" action="" enctype="multipart/form-data" class="mf-form">
            <?php if ($edit_mode): ?>
                <input type="hidden" name="action" value="edit_lapangan">
                <input type="hidden" name="lapangan_id" value="<?php echo $edit_data->id; ?>">
                <?php wp_nonce_field('edit_lapangan_action', 'mf_nonce'); ?>
            <?php else: ?>
                <input type="hidden" name="action" value="add_lapangan">
                <?php wp_nonce_field('add_lapangan_action', 'mf_nonce'); ?>
            <?php endif; ?>
            
            <div class="mf-form-group">
                <label for="nama">
                    Nama Lapangan <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama" 
                    id="nama" 
                    value="<?php echo $edit_mode ? esc_attr($edit_data->nama) : ''; ?>"
                    required
                    placeholder="Contoh: Lapangan A"
                >
                <small>Minimal 3 karakter, maksimal 100 karakter</small>
            </div>
            
            <div class="mf-form-group">
                <label for="jenis_lapangan">
                    Jenis Lapangan <span class="required">*</span>
                </label>
                <select name="jenis_lapangan" id="jenis_lapangan" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="Vinyl" <?php echo ($edit_mode && $edit_data->jenis_lapangan === 'Vinyl') ? 'selected' : ''; ?>>Vinyl</option>
                    <option value="Sintetis" <?php echo ($edit_mode && $edit_data->jenis_lapangan === 'Sintetis') ? 'selected' : ''; ?>>Sintetis</option>
                    <option value="Rumput" <?php echo ($edit_mode && $edit_data->jenis_lapangan === 'Rumput') ? 'selected' : ''; ?>>Rumput</option>
                </select>
            </div>
            
            <div class="mf-form-group">
                <label for="harga">
                    Harga per Jam (Rp) <span class="required">*</span>
                </label>
                <input 
                    type="number" 
                    name="harga" 
                    id="harga" 
                    value="<?php echo $edit_mode ? esc_attr($edit_data->harga) : ''; ?>"
                    required
                    min="0"
                    max="9999999"
                    placeholder="100000"
                >
                <small>Maksimal Rp 9.999.999</small>
            </div>
            
            <div class="mf-form-group">
                <label for="status">
                    Status <span class="required">*</span>
                </label>
                <select name="status" id="status" required>
                    <option value="aktif" <?php echo ($edit_mode && $edit_data->status === 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                    <option value="nonaktif" <?php echo ($edit_mode && $edit_data->status === 'nonaktif') ? 'selected' : ''; ?>>Nonaktif</option>
                </select>
            </div>
            
            <div class="mf-form-group">
                <label for="foto">
                    Foto Lapangan
                </label>
                <input 
                    type="file" 
                    name="foto" 
                    id="foto" 
                    class="mf-image-upload"
                    accept="image/jpeg,image/jpg,image/png,image/gif"
                >
                <small>Format: JPG, PNG, GIF. Maksimal 2MB. (Opsional)</small>
                
                <div class="mf-image-preview">
                    <?php if ($edit_mode && !empty($edit_data->foto)): ?>
                        <img src="<?php echo $upload_dir['baseurl'] . '/mari-futsal/' . $edit_data->foto; ?>" 
                             style="max-width: 300px; max-height: 300px; border-radius: 6px; margin-top: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <p><small>Foto saat ini. Upload foto baru untuk menggantinya.</small></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mf-form-actions">
                <button type="submit" class="mf-btn mf-btn-primary">
                    <span class="dashicons dashicons-yes"></span>
                    <?php echo $edit_mode ? 'Update Lapangan' : 'Simpan Lapangan'; ?>
                </button>
                <a href="<?php echo admin_url('admin.php?page=mari-futsal-lapangan'); ?>" class="mf-btn mf-btn-secondary">
                    <span class="dashicons dashicons-no-alt"></span>
                    Batal
                </a>
            </div>
        </form>
    </div>
    
    <!-- Table Lapangan -->
    <div class="mf-card">
        <h3>Data Lapangan (<?php echo count($lapangan_list); ?> Total)</h3>
        
        <?php if ($lapangan_list): ?>
        <div class="mf-table-wrapper">
            <table class="wp-list-table widefat fixed striped mf-table">
                <thead>
                    <tr>
                        <th style="width: 8%;">ID</th>
                        <th style="width: 20%;">Nama Lapangan</th>
                        <th style="width: 15%;">Jenis</th>
                        <th style="width: 15%;">Harga / Jam</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 12%;">Foto</th>
                        <th style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lapangan_list as $lap): ?>
                    <tr>
                        <td data-label="ID"><?php echo $lap->id; ?></td>
                        <td data-label="Nama Lapangan"><strong><?php echo esc_html($lap->nama); ?></strong></td>
                        <td data-label="Jenis"><?php echo esc_html($lap->jenis_lapangan); ?></td>
                        <td data-label="Harga / Jam"><strong><?php echo MF_Functions::format_rupiah($lap->harga); ?></strong></td>
                        <td data-label="Status">
                            <?php if ($lap->status === 'aktif'): ?>
                                <span class="mf-badge mf-badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="mf-badge mf-badge-danger">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Foto">
                            <?php if (!empty($lap->foto)): ?>
                                <img src="<?php echo $upload_dir['baseurl'] . '/mari-futsal/' . $lap->foto; ?>" 
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                     onclick="window.open(this.src, '_blank')">
                            <?php else: ?>
                                <span style="color: #999;">No image</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="Actions">
                            <a href="<?php echo admin_url('admin.php?page=mari-futsal-lapangan&edit=1&id=' . $lap->id); ?>" 
                               class="mf-btn mf-btn-small mf-btn-primary">
                                <span class="dashicons dashicons-edit"></span>
                                Edit
                            </a>
                            <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=mari-futsal-lapangan&action=delete&id=' . $lap->id), 'delete_lapangan_' . $lap->id); ?>" 
                               class="mf-btn mf-btn-small mf-btn-danger mf-delete-btn"
                               data-item-name="<?php echo esc_attr($lap->nama); ?>">
                                <span class="dashicons dashicons-trash"></span>
                                Delete
                            </a>
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
