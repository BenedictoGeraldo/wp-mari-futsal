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

<div class="wp-site-blocks">
    <div class="wp-block-group" style="padding: 2rem 1rem; max-width: 1000px; margin: 0 auto;">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="<?php echo esc_url(home_url('/lapangan/')); ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Lapangan
            </a>
        </div>

        <div class="grid md:grid-cols-5 gap-8">
            
            <!-- Left: Lapangan Info -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-4">
                    <img src="<?php echo esc_url($foto_url); ?>" alt="<?php echo esc_attr($lapangan->nama); ?>" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">
                            <?php echo esc_html($lapangan->nama); ?>
                        </h2>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span><?php echo esc_html($lapangan->jenis_lapangan); ?></span>
                            </div>
                            
                            <div class="flex items-center text-gray-600">
                                <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-2xl font-bold text-green-600">
                                    <?php echo mf_format_rupiah($lapangan->harga); ?>
                                </span>
                                <span class="ml-2 text-sm">/jam</span>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <h3 class="font-semibold mb-2 text-gray-900">Fasilitas:</h3>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Lapangan Premium Grade Internasioinal
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Penerangan Lampu Sorot
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Kamar Mandi & Shower
                                </li>
                                <li class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Area Parkir Luas
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Booking Form -->
            <div class="md:col-span-3">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Form Booking</h2>

                    <!-- Alert Container -->
                    <div id="mf-alert-container"></div>

                    <form id="mf-booking-form" class="space-y-6">
                        <!-- Hidden Fields -->
                        <input type="hidden" name="lapangan_id" value="<?php echo esc_attr($lapangan_id); ?>">
                        <input type="hidden" name="harga_per_jam" value="<?php echo esc_attr($lapangan->harga); ?>">
                        <input type="hidden" name="jadwal_id" id="selected-jadwal-id" value="">

                        <!-- Tanggal -->
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-2" for="tanggal-booking">
                                Pilih Tanggal <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                id="tanggal-booking" 
                                name="tanggal" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                min="<?php echo $today; ?>"
                                required
                            >
                            <p class="text-xs text-gray-500 mt-1">Minimal booking hari ini</p>
                        </div>

                        <!-- Slot Waktu -->
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-2">
                                Pilih Jam Main <span class="text-red-500">*</span>
                            </label>
                            <div id="loading-slots" class="text-center py-8 hidden">
                                <div class="inline-block animate-spin rounded-full h-10 w-10 border-b-2 border-green-600"></div>
                                <p class="text-gray-600 mt-3">Memuat slot waktu...</p>
                            </div>
                            <div id="slot-container" class="grid grid-cols-3 md:grid-cols-4 gap-3">
                                <p class="text-gray-500 text-center py-6 col-span-full">Silakan pilih tanggal terlebih dahulu</p>
                            </div>
                        </div>

                        <!-- Nama -->
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-2" for="nama-customer">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="nama-customer" 
                                name="nama" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                placeholder="Masukkan nama lengkap Anda"
                                required
                            >
                        </div>

                        <!-- No HP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-800 mb-2" for="no-hp-customer">
                                Nomor HP/WA <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                id="no-hp-customer" 
                                name="no_hp" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                placeholder="08xxxxxxxxxx"
                                pattern="[0-9]{10,13}"
                                required
                            >
                            <p class="text-xs text-gray-500 mt-1">Format: 08xxxxxxxxxx (10-13 digit)</p>
                        </div>

                        <!-- Total Harga -->
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium">Total Pembayaran:</span>
                                <span id="total-harga" class="text-3xl font-bold text-green-600">
                                    <?php echo mf_format_rupiah($lapangan->harga); ?>
                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">* Pembayaran di tempat saat datang</p>
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            id="btn-submit-booking"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center"
                        >
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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