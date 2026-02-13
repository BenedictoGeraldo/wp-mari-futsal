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
    <h1 class="wp-heading-inline">Kelola Booking</h1>
    <hr class="wp-header-end">

    <!-- Filter Section -->
    <div class="tablenav top">
        <form method="get" action="">
            <input type="hidden" name="page" value="mari-futsal-booking">
            
            <div style="display: flex; gap: 10px; align-items: center; margin: 15px 0;">
                <div>
                    <label>Dari Tanggal:</label>
                    <input type="date" name="tanggal_dari" value="<?php echo esc_attr($filter_tanggal_dari); ?>" class="regular-text">
                </div>
                
                <div>
                    <label>Sampai Tanggal:</label>
                    <input type="date" name="tanggal_sampai" value="<?php echo esc_attr($filter_tanggal_sampai); ?>" class="regular-text">
                </div>
                
                <div>
                    <label>Lapangan:</label>
                    <select name="lapangan_id">
                        <option value="0">Semua Lapangan</option>
                        <?php foreach ($all_lapangan as $lap) : ?>
                            <option value="<?php echo $lap->id; ?>" <?php selected($filter_lapangan, $lap->id); ?>>
                                <?php echo esc_html($lap->nama); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <button type="submit" class="button button-primary">Filter</button>
                    <a href="?page=mari-futsal-booking" class="button">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Count -->
    <p class="search-box">
        <strong>Total: <?php echo number_format($total_items); ?> booking ditemukan</strong>
    </p>

    <!-- Table -->
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th width="10%">Kode Booking</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Lapangan</th>
                <th width="10%">Jam</th>
                <th width="15%">Nama Customer</th>
                <th width="12%">No HP</th>
                <th width="10%">Total Harga</th>
                <th width="12%">Dibuat</th>
                <th width="8%">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($bookings)) : ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 30px;">
                        <strong>Tidak ada booking ditemukan.</strong>
                        <br>
                        <?php if (!empty($filter_tanggal_dari) || !empty($filter_tanggal_sampai) || $filter_lapangan > 0) : ?>
                            <a href="?page=mari-futsal-booking" class="button" style="margin-top: 10px;">Reset Filter</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else : ?>
                <?php foreach ($bookings as $booking) : 
                    $tanggal_formatted = date('d M Y', strtotime($booking->tanggal));
                    $created_formatted = date('d M Y H:i', strtotime($booking->created_at));
                ?>
                    <tr>
                        <td><strong><?php echo esc_html($booking->kode_booking); ?></strong></td>
                        <td><?php echo $tanggal_formatted; ?></td>
                        <td>
                            <strong><?php echo esc_html($booking->lapangan_nama); ?></strong>
                            <br>
                            <small><?php echo esc_html($booking->jenis_lapangan); ?></small>
                        </td>
                        <td>
                            <?php echo date('H:i', strtotime($booking->jam_mulai)); ?> - 
                            <?php echo date('H:i', strtotime($booking->jam_selesai)); ?>
                        </td>
                        <td><?php echo esc_html($booking->nama); ?></td>
                        <td><?php echo esc_html($booking->no_hp); ?></td>
                        <td><strong>Rp <?php echo number_format($booking->total_harga, 0, ',', '.'); ?></strong></td>
                        <td><small><?php echo $created_formatted; ?></small></td>
                        <td>
                            <a href="?page=mari-futsal-booking&action=view&id=<?php echo $booking->id; ?>" class="button button-small">
                                View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($total_pages > 1) : ?>
        <div class="tablenav bottom">
            <div class="tablenav-pages">
                <span class="displaying-num"><?php echo number_format($total_items); ?> items</span>
                <span class="pagination-links">
                    <?php
                    $base_url = add_query_arg(array(
                        'page' => 'mari-futsal-booking',
                        'tanggal_dari' => $filter_tanggal_dari,
                        'tanggal_sampai' => $filter_tanggal_sampai,
                        'lapangan_id' => $filter_lapangan
                    ), admin_url('admin.php'));
                    
                    // Previous
                    if ($current_page > 1) {
                        echo '<a class="prev-page button" href="' . esc_url(add_query_arg('paged', $current_page - 1, $base_url)) . '">‹</a> ';
                    } else {
                        echo '<span class="tablenav-pages-navspan button disabled">‹</span> ';
                    }
                    
                    // Page numbers
                    echo '<span class="paging-input">';
                    echo '<span class="tablenav-paging-text">' . $current_page . ' of ' . $total_pages . '</span>';
                    echo '</span> ';
                    
                    // Next
                    if ($current_page < $total_pages) {
                        echo '<a class="next-page button" href="' . esc_url(add_query_arg('paged', $current_page + 1, $base_url)) . '">›</a>';
                    } else {
                        echo '<span class="tablenav-pages-navspan button disabled">›</span>';
                    }
                    ?>
                </span>
            </div>
        </div>
    <?php endif; ?>

</div>

<style>
.wp-list-table td {
    vertical-align: middle;
}
</style>