<?php
/**
 * Template Name: Daftar Lapangan
 * Description: Halaman untuk menampilkan semua lapangan futsal yang tersedia
 */

get_header();

// Get all active lapangan
$lapangan_list = mf_get_all_lapangan_aktif();
?>

<div class="mf-lapangan-page">
    <div class="mf-container">
        
        <!-- Header Section -->
        <div class="mf-lapangan-header">
            <h1>Pilih Lapangan Futsal</h1>
            <p>Tersedia <?php echo count($lapangan_list); ?> lapangan berkualitas untuk kebutuhan futsal Anda</p>
        </div>

        <?php if (empty($lapangan_list)) : ?>
            <!-- Empty State -->
            <div class="mf-empty-state">
                <svg class="w-16 h-16 mx-auto mb-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-semibold mb-2">Belum Ada Lapangan Tersedia</h3>
                <p class="text-gray-600">Silakan hubungi admin untuk informasi lebih lanjut.</p>
            </div>
        <?php else : ?>
            
            <!-- Lapangan Grid -->
            <div class="mf-lapangan-grid">
                <?php foreach ($lapangan_list as $lapangan) : 
                    if (!empty($lapangan->foto)) {
                        $upload_dir = wp_upload_dir();
                        $foto_url = $upload_dir['baseurl'] . '/mari-futsal/' . $lapangan->foto;
                    } else {
                        $foto_url = 'https://via.placeholder.com/400x300?text=Lapangan+Futsal';
                    }
                ?>
                    <div class="mf-lapangan-card">
                        <!-- Foto Lapangan -->
                        <div class="mf-lapangan-card-image">
                            <img 
                                src="<?php echo esc_url($foto_url); ?>" 
                                alt="<?php echo esc_attr($lapangan->nama); ?>"
                            >
                            <!-- Badge Status -->
                            <div class="mf-lapangan-card-badge">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                    ✓ Tersedia
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="mf-lapangan-card-body">
                            <h3 class="mf-lapangan-card-title">
                                <?php echo esc_html($lapangan->nama); ?>
                            </h3>
                            
                            <div class="mf-lapangan-card-meta">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span><?php echo esc_html($lapangan->jenis_lapangan); ?></span>
                            </div>

                            <!-- Harga -->
                            <div class="mf-lapangan-card-price">
                                <div class="price">
                                    <?php echo mf_format_rupiah($lapangan->harga); ?>
                                </div>
                                <div class="period">per jam</div>
                            </div>

                            <!-- Tombol Booking -->
                            <a 
                                href="<?php echo esc_url(add_query_arg('lapangan_id', $lapangan->id, home_url('/booking/'))); ?>" 
                                class="mf-btn-primary"
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