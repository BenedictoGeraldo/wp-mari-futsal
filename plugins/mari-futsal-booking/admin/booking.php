<?php
/**
 * Kelola Booking Page
 * List semua booking dengan filter
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

global $wpdb;

if (isset($_GET['action']) && $_GET['action'] === 'view') {
    require_once MF_PLUGIN_DIR . 'admin/detail-booking.php';
    return;
} 

$filter_tanggal_dari = isset($_GET['tanggal_dari']) ? sanitize_text_field($_GET['tanggal_dari']) : '';
$filter_tanggal_sampai = isset($_GET['tanggal_sampai']) ? sanitize_text_field($_GET['tanggal_sampai']) : '';
$filter_lapangan = isset($_GET['lapangan_id']) ? intval($_GET['lapangan_id']) : 0;

$per_page = 20;
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$offset = ($current_page - 1) * $per_page;

$booking_table = $wpdb->prefix . 'futsal_booking';
$lapangan_table = $wpdb->prefix . 'futsal_lapangan';
$jadwal_table = $wpdb->prefix . 'futsal_jadwal';

$where = array('1=1');
$params = array();

if (!empty($filter_tanggal_dari)){
    $where[] = 'b.tanggal >= %s';
    $params[] = $filter_tanggal_dari;
}

if (!empty($filter_tanggal_sampai)) {
    $where[] = 'b.tanggal <= %s';
    $params[] = $filter_tanggal_sampai;
}

if ($filter_lapangan > 0) {
    $where[] = 'b.lapangan_id = %d';
    $params[] = $filter_lapangan;
}

$where_clause = implode(' AND ', $where);

$count_query = "SELECT COUNT(*) FROM $booking_table b WHERE $where_clause";
if(!empty($params)) {
    $count_query = $wpdb->prepare($count_query, $params);
}
$total_items = $wpdb->get_var($count_query);
$total_pages = ceil($total_items / $per_page);

$query = "
    SELECT
        b.*,
        l.nama as lapangan_nama,
        l.jenis_lapangan,
        j.jam_mulai,
        j.jam_selesai
    FROM $booking_table b
    LEFT JOIN $lapangan_table l ON b.lapangan_id = l.id
    LEFT JOIN $jadwal_table j ON b.jadwal_id = j.id
    WHERE $where_clause
    ORDER BY b.tanggal DESC, j.jam_mulai ASC
    LIMIT %d OFFSET %d
";

$params[] = $per_page;
$params[] = $offset;

$bookings = $wpdb->get_results($wpdb->prepare($query, $params));

$all_lapangan = $wpdb-> get_results("SELECT id, nama FROM $lapangan_table ORDER BY nama ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <span class="dashicons dashicons-list-view"></span>
        Kelola Booking
    </h1>
    <hr class="wp-header-end">

    <!-- Filter Card -->
    <div class="mf-card">
        <h3>
            <span class="dashicons dashicons-filter"></span>
            Filter Booking
        </h3>
        <form method="get" action="" class="mf-filter-form">
            <input type="hidden" name="page" value="mari-futsal-booking">
            
            <div class="mf-filter-group">
                <div class="mf-form-group">
                    <label for="tanggal_dari">Dari Tanggal:</label>
                    <input type="date" id="tanggal_dari" name="tanggal_dari" value="<?php echo esc_attr($filter_tanggal_dari); ?>">
                </div>
                
                <div class="mf-form-group">
                    <label for="tanggal_sampai">Sampai Tanggal:</label>
                    <input type="date" id="tanggal_sampai" name="tanggal_sampai" value="<?php echo esc_attr($filter_tanggal_sampai); ?>">
                </div>
                
                <div class="mf-form-group">
                    <label for="lapangan_id">Lapangan:</label>
                    <select id="lapangan_id" name="lapangan_id">
                        <option value="0">Semua Lapangan</option>
                        <?php foreach ($all_lapangan as $lap) : ?>
                            <option value="<?php echo $lap->id; ?>" <?php selected($filter_lapangan, $lap->id); ?>>
                                <?php echo esc_html($lap->nama); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mf-form-group mf-filter-actions">
                    <label>&nbsp;</label>
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="mf-btn mf-btn-primary">
                            <span class="dashicons dashicons-search"></span>
                            Filter
                        </button>
                        <a href="?page=mari-futsal-booking" class="mf-btn mf-btn-secondary">
                            <span class="dashicons dashicons-image-rotate"></span>
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="mf-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0;">
                <span class="dashicons dashicons-tickets-alt"></span>
                Data Booking
            </h3>
            <div style="color: #666;">
                <strong><?php echo number_format($total_items); ?></strong> booking ditemukan
            </div>
        </div>
        
        <?php if ($bookings): ?>
        <div class="mf-table-wrapper">
            <table class="wp-list-table widefat fixed striped mf-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Kode Booking</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 15%;">Lapangan</th>
                    <th style="width: 10%;">Jam</th>
                    <th style="width: 15%;">Nama Customer</th>
                    <th style="width: 12%;">No HP</th>
                    <th style="width: 12%;">Total Harga</th>
                    <th style="width: 10%;">Dibuat</th>
                    <th style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $booking) : 
                    $tanggal_formatted = date('d M Y', strtotime($booking->tanggal));
                    $created_formatted = date('d M Y H:i', strtotime($booking->created_at));
                ?>
                    <tr>
                        <td data-label="Kode Booking">
                            <code><?php echo esc_html($booking->kode_booking); ?></code>
                        </td>
                        <td data-label="Tanggal"><?php echo $tanggal_formatted; ?></td>
                        <td data-label="Lapangan">
                            <strong><?php echo esc_html($booking->lapangan_nama); ?></strong>
                            <br>
                            <small style="color: #666;"><?php echo esc_html($booking->jenis_lapangan); ?></small>
                        </td>
                        <td data-label="Jam">
                            <span class="dashicons dashicons-clock" style="color: #2271b1; font-size: 14px;"></span>
                            <?php echo date('H:i', strtotime($booking->jam_mulai)); ?> - 
                            <?php echo date('H:i', strtotime($booking->jam_selesai)); ?>
                        </td>
                        <td data-label="Nama Customer"><?php echo esc_html($booking->nama); ?></td>
                        <td data-label="No HP"><?php echo esc_html($booking->no_hp); ?></td>
                        <td data-label="Total Harga"><strong>Rp <?php echo number_format($booking->total_harga, 0, ',', '.'); ?></strong></td>
                        <td data-label="Dibuat"><small style="color: #666;"><?php echo $created_formatted; ?></small></td>
                        <td data-label="Actions">
                            <a href="?page=mari-futsal-booking&action=view&id=<?php echo $booking->id; ?>" class="mf-btn mf-btn-small mf-btn-primary">
                                <span class="dashicons dashicons-visibility"></span>
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php else: ?>
        <div class="mf-empty-state">
            <span class="dashicons dashicons-tickets-alt" style="font-size: 64px; opacity: 0.3;"></span>
            <h3>Tidak ada booking ditemukan</h3>
            <?php if (!empty($filter_tanggal_dari) || !empty($filter_tanggal_sampai) || $filter_lapangan > 0) : ?>
                <p>Coba ubah filter atau klik tombol Reset untuk melihat semua booking.</p>
                <a href="?page=mari-futsal-booking" class="mf-btn mf-btn-secondary">
                    <span class="dashicons dashicons-image-rotate"></span>
                    Reset Filter
                </a>
            <?php else: ?>
                <p>Belum ada booking yang tersedia.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1) : ?>
        <div class="mf-card" style="padding: 15px;">
            <div style="display: flex; justify-content: center; align-items: center; gap: 15px;">
                <?php
                $base_url = add_query_arg(array(
                    'page' => 'mari-futsal-booking',
                    'tanggal_dari' => $filter_tanggal_dari,
                    'tanggal_sampai' => $filter_tanggal_sampai,
                    'lapangan_id' => $filter_lapangan
                ), admin_url('admin.php'));
                
                // Previous
                if ($current_page > 1) {
                    echo '<a class="mf-btn mf-btn-secondary mf-btn-small" href="' . esc_url(add_query_arg('paged', $current_page - 1, $base_url)) . '">
                        <span class="dashicons dashicons-arrow-left-alt2"></span> Prev
                    </a>';
                } else {
                    echo '<span class="mf-btn mf-btn-secondary mf-btn-small mf-btn-disabled">
                        <span class="dashicons dashicons-arrow-left-alt2"></span> Prev
                    </span>';
                }
                
                // Page numbers
                echo '<span style="color: #666; font-weight: 600;">Halaman ' . $current_page . ' dari ' . $total_pages . '</span>';
                
                // Next
                if ($current_page < $total_pages) {
                    echo '<a class="mf-btn mf-btn-secondary mf-btn-small" href="' . esc_url(add_query_arg('paged', $current_page + 1, $base_url)) . '">
                        Next <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </a>';
                } else {
                    echo '<span class="mf-btn mf-btn-secondary mf-btn-small mf-btn-disabled">
                        Next <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </span>';
                }
                ?>
            </div>
        </div>
    <?php endif; ?>

</div>

<style>
/* Filter Form Styles */
.mf-filter-form {
    margin-top: 15px;
}

.mf-filter-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.mf-filter-actions {
    display: flex;
    align-items: flex-end;
}

/* Table Actions Cell Styling */
.mf-table td[data-label="Actions"] {
    padding-left: 15px !important;
    padding-right: 15px !important;
    text-align: center;
}

.mf-table td[data-label="Actions"] .mf-btn {
    display: inline-flex !important;
    width: auto !important;
    min-width: auto !important;
    margin: 0 auto;
}

@media screen and (max-width: 782px) {
    .mf-filter-group {
        grid-template-columns: 1fr;
    }
    
    .mf-filter-actions > div {
        flex-direction: row;
        width: 100%;
        gap: 8px;
    }
    
    .mf-filter-actions .mf-btn {
        width: auto;
        flex: 1;
        font-size: 12px;
        padding: 8px 12px;
    }
    
    /* Override table button untuk Actions column */
    #wpwrap .mf-table td[data-label="Actions"] {
        padding: 15px !important;
        text-align: center !important;
    }
    
    #wpwrap .mf-table td[data-label="Actions"] .mf-btn,
    #wpwrap .mf-table td[data-label="Actions"] .mf-btn-small {
        display: inline-flex !important;
        width: auto !important;
        min-width: auto !important;
        max-width: none !important;
        margin: 0 auto !important;
        font-size: 11px !important;
        padding: 6px 10px !important;
    }
}
</style>