<?php
/**
 * Template Name: Daftar Lapangan
 * Description: Halaman untuk menampilkan semua lapangan futsal yang tersedia
 */

get_header();

// Get all active lapangan
$lapangan_list = mf_get_all_lapangan_aktif();
?>

<div class="wp-site-blocks">
    <div class="wp-block-group" style="padding: 4rem 1rem; max-width: 1200px; margin: 0 auto;">
        
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                Pilih Lapangan Futsal
            </h1>
            <p class="text-lg text-gray-600">
                Tersedia <?php echo count($lapangan_list); ?> lapangan berkualitas untuk kebutuhan futsal Anda
            </p>
        </div>

        <?php if (empty($lapangan_list)) : ?>
            <!-- Empty State -->
            <div class="text-center py-12 bg-blue-50 rounded-lg">
                <svg class="w-16 h-16 mx-auto mb-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold mb-2">Belum Ada Lapangan Tersedia</h3>
                <p class="text-gray-600">Silakan hubungi admin untuk informasi lebih lanjut.</p>
            </div>
        <?php else : ?>
            
            <!-- Lapangan Grid -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($lapangan_list as $lapangan) : 
                    if (!empty($lapangan->foto)) {
                        $upload_dir = wp_upload_dir();
                        $foto_url = $upload_dir['baseurl'] . '/mari-futsal/' . $lapangan->foto;
                    } else {
                        $foto_url = 'https://via.placeholder.com/400x300?text=Lapangan+Futsal';
                    }
                ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Foto Lapangan -->
                        <div class="relative h-48 overflow-hidden">
                            <img 
                                src="<?php echo esc_url($foto_url); ?>" 
                                alt="<?php echo esc_attr($lapangan->nama); ?>"
                                class="w-full h-full object-cover"
                            >
                            <!-- Badge Status -->
                            <div class="absolute top-3 right-3">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                    ✓ Tersedia
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-2">
                                <?php echo esc_html($lapangan->nama); ?>
                            </h3>
                            
                            <div class="flex items-center text-gray-600 mb-3">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="text-sm"><?php echo esc_html($lapangan->jenis_lapangan); ?></span>
                            </div>

                            <!-- Harga -->
                            <div class="mb-4">
                                <div class="text-3xl font-bold text-green-600">
                                    <?php echo mf_format_rupiah($lapangan->harga); ?>
                                </div>
                                <div class="text-sm text-gray-600">per jam</div>
                            </div>

                            <!-- Tombol Booking -->
                            <a 
                                href="<?php echo esc_url(add_query_arg('lapangan_id', $lapangan->id, home_url('/booking/'))); ?>" 
                                class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200"
                            >
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Booking Sekarang
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>

        <!-- Info Section -->
        <div class="mt-16 bg-gradient-to-r from-green-50 to-blue-50 p-8 rounded-lg">
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-green-600 mb-3">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Booking Mudah</h3>
                    <p class="text-gray-600 text-sm">Proses booking cepat dan real-time</p>
                </div>
                <div class="text-center">
                    <div class="text-green-600 mb-3">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Harga Terjangkau</h3>
                    <p class="text-gray-600 text-sm">Harga kompetitif dengan fasilitas terbaik</p>
                </div>
                <div class="text-center">
                    <div class="text-green-600 mb-3">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-lg mb-2">Lapangan Berkualitas</h3>
                    <p class="text-gray-600 text-sm">Rumput sintetis premium dan terawat</p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php get_footer(); ?>