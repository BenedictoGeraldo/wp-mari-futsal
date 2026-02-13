<?php
/**
 * Template Name: Form Booking
 * Description: Halaman form booking lapangan dengan real-time availability
 */

get_header();

// Get lapangan_id from URL
$lapangan_id = isset($_GET['lapangan_id']) ? intval($_GET['lapangan_id']) : 0;

if (!$lapangan_id) {
    // Redirect ke halaman lapangan jika tidak ada ID
    wp_redirect(home_url('/lapangan/'));
    exit;
}

$lapangan = mf_get_lapangan($lapangan_id);

if (!$lapangan || $lapangan->status != 'aktif') {
    // Redirect jika lapangan tidak ditemukan atau tidak aktif
    wp_redirect(home_url('/lapangan/'));
    exit;
}

if (!empty($lapangan->foto)) {
    $upload_dir = wp_upload_dir();
    $foto_url = $upload_dir['baseurl'] . '/mari-futsal/' . $lapangan->foto;
} else {
    $foto_url = 'https://via.placeholder.com/400x200?text=Lapangan+Futsal';
}
$today = date('Y-m-d');
?>

<div class="mf-booking-page">
    <div class="mf-container-md">
        
        <!-- Back Button -->
        <div style="margin-bottom: 1.5rem;">
            <a href="<?php echo esc_url(home_url('/lapangan/')); ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900" style="text-decoration: none; transition: color 0.2s;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Lapangan
            </a>
        </div>

        <div class="mf-booking-layout">
            
            <!-- Sidebar: Lapangan Info -->
            <div class="mf-booking-sidebar">
                <div class="mf-booking-sidebar-card">
                    <img src="<?php echo esc_url($foto_url); ?>" alt="<?php echo esc_attr($lapangan->nama); ?>">
                    <div class="mf-booking-sidebar-content">
                        <h2 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 1rem;">
                            <?php echo esc_html($lapangan->nama); ?>
                        </h2>
                        
                        <div style="margin-bottom: 1.5rem;">
                            <div class="flex items-center text-gray-600" style="margin-bottom: 0.75rem;">
                                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span><?php echo esc_html($lapangan->jenis_lapangan); ?></span>
                            </div>
                            
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span style="font-size: 1.5rem; font-weight: 700; color: #059669;">
                                    <?php echo mf_format_rupiah($lapangan->harga); ?>
                                </span>
                                <span style="margin-left: 0.5rem; font-size: 0.875rem;">/jam</span>
                            </div>
                        </div>

                        <div style="border-top: 1px solid #e5e7eb; padding-top: 1rem;">
                            <h3 style="font-weight: 600; margin-bottom: 0.5rem; color: #1f2937;">Fasilitas:</h3>
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                <li class="flex items-center" style="margin-bottom: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink: 0;">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Lapangan Premium Grade International
                                </li>
                                <li class="flex items-center" style="margin-bottom: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink: 0;">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Penerangan Lampu Sorot
                                </li>
                                <li class="flex items-center" style="margin-bottom: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink: 0;">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Kamar Mandi & Shower
                                </li>
                                <li class="flex items-center" style="font-size: 0.875rem; color: #6b7280;">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20" style="flex-shrink: 0;">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Area Parkir Luas
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main: Booking Form -->
            <div>
                <div class="mf-booking-form-card">
                    <h2>Form Booking</h2>

                    <!-- Alert Container -->
                    <div id="mf-alert-container"></div>

                    <form id="mf-booking-form" style="margin-top: 1.5rem;">
                        <!-- Hidden Fields -->
                        <input type="hidden" name="lapangan_id" value="<?php echo esc_attr($lapangan_id); ?>">
                        <input type="hidden" name="harga_per_jam" value="<?php echo esc_attr($lapangan->harga); ?>">
                        <input type="hidden" name="jadwal_id" id="selected-jadwal-id" value="">

                        <!-- Tanggal -->
                        <div class="mf-form-group">
                            <label for="tanggal-booking">
                                Pilih Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="tanggal-booking" 
                                name="tanggal" 
                                min="<?php echo $today; ?>"
                                required
                            >
                            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Minimal booking hari ini</p>
                        </div>

                        <!-- Slot Waktu -->
                        <div class="mf-form-group">
                            <label>
                                Pilih Jam Main <span class="text-red-500">*</span>
                            </label>
                            <div id="loading-slots" class="mf-loading hidden">
                                <div class="mf-spinner"></div>
                                <p style="color: #6b7280; margin-top: 0.75rem;">Memuat slot waktu...</p>
                            </div>
                            <div id="slot-container" class="mf-slot-grid">
                                <p style="text-align: center; color: #6b7280; padding: 1.5rem; grid-column: 1 / -1;">Silakan pilih tanggal terlebih dahulu</p>
                            </div>
                        </div>

                        <!-- Nama -->
                        <div class="mf-form-group">
                            <label for="nama-customer">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nama-customer" 
                                name="nama" 
                                placeholder="Masukkan nama lengkap Anda"
                                required
                            >
                        </div>

                        <!-- No HP -->
                        <div class="mf-form-group">
                            <label for="no-hp-customer">
                                Nomor HP/WA <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                id="no-hp-customer" 
                                name="no_hp" 
                                placeholder="08xxxxxxxxxx"
                                pattern="[0-9]{10,13}"
                                required
                            >
                            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.25rem;">Format: 08xxxxxxxxxx (10-13 digit)</p>
                        </div>

                        <!-- Total Harga -->
                        <div style="background: linear-gradient(135deg, #f0fdf4 0%, #dbeafe 100%); padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                                <span style="color: #374151; font-weight: 600;">Total Pembayaran:</span>
                                <span id="total-harga" style="font-size: 1.875rem; font-weight: 700; color: #059669;">
                                    <?php echo mf_format_rupiah($lapangan->harga); ?>
                                </span>
                            </div>
                            <p style="font-size: 0.75rem; color: #6b7280; margin-top: 0.5rem; margin-bottom: 0;">* Pembayaran di tempat saat datang</p>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            id="btn-submit-booking"
                            class="mf-btn-primary"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Konfirmasi Booking
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<?php get_footer(); ?>