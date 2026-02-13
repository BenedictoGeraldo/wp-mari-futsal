<?php
/**
 * Template Name: Konfirmasi Booking
 * Description: Halaman konfirmasi setelah booking berhasil
 */

get_header();

// Get booking data from URL parameters
$kode_booking = isset($_GET['kode']) ? sanitize_text_field($_GET['kode']) : '';

if (empty($kode_booking)) {
    wp_redirect(home_url('/lapangan/'));
    exit;
}

// Get booking details from database
global $wpdb;
$booking_table = $wpdb->prefix . 'futsal_booking';
$booking = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM $booking_table WHERE kode_booking = %s",
    $kode_booking
));

if (!$booking) {
    wp_redirect(home_url('/lapangan/'));
    exit;
}

// Get related data
$lapangan = mf_get_lapangan($booking->lapangan_id);
$jadwal = mf_get_jadwal($booking->jadwal_id);

// Format tanggal Indonesia
$tanggal_formatted = date('d F Y', strtotime($booking->tanggal));
$bulan_indonesia = array(
    'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
    'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
    'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
    'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
);
$tanggal_formatted = str_replace(array_keys($bulan_indonesia), array_values($bulan_indonesia), $tanggal_formatted);
?>

<div class="wp-site-blocks">
    <div class="wp-block-group" style="padding: 4rem 1rem; max-width: 800px; margin: 0 auto;">
        
        <!-- Success Animation -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 bg-green-100 rounded-full mb-4">
                <svg class="w-20 h-20 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-3">Booking Berhasil!</h1>
            <p class="text-lg text-gray-600">Terima kasih telah melakukan booking di Mari Futsal</p>
        </div>

        <!-- Booking Details Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-green-600 to-blue-600 text-white p-6 text-center">
                <p class="text-sm mb-2 opacity-90">Kode Booking Anda</p>
                <h2 class="text-4xl font-bold tracking-wider"><?php echo esc_html($kode_booking); ?></h2>
                <p class="text-sm mt-2 opacity-90">Simpan kode ini sebagai bukti booking</p>
            </div>

            <div class="p-6 space-y-4">
                <!-- Nama -->
                <div class="flex border-b pb-4">
                    <div class="w-1/3 text-gray-600">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Nama
                    </div>
                    <div class="w-2/3 font-semibold text-gray-900">
                        <?php echo esc_html($booking->nama); ?>
                    </div>
                </div>

                <!-- No HP -->
                <div class="flex border-b pb-4">
                    <div class="w-1/3 text-gray-600">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        No HP/WA
                    </div>
                    <div class="w-2/3 font-semibold text-gray-900">
                        <?php echo esc_html($booking->no_hp); ?>
                    </div>
                </div>

                <!-- Lapangan -->
                <div class="flex border-b pb-4">
                    <div class="w-1/3 text-gray-600">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Lapangan
                    </div>
                    <div class="w-2/3">
                        <div class="font-semibold text-gray-900"><?php echo esc_html($lapangan->nama); ?></div>
                        <div class="text-sm text-gray-600"><?php echo esc_html($lapangan->jenis_lapangan); ?></div>
                    </div>
                </div>

                <!-- Tanggal -->
                <div class="flex border-b pb-4">
                    <div class="w-1/3 text-gray-600">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Tanggal
                    </div>
                    <div class="w-2/3 font-semibold text-gray-900">
                        <?php echo $tanggal_formatted; ?>
                    </div>
                </div>

                <!-- Jam -->
                <div class="flex border-b pb-4">
                    <div class="w-1/3 text-gray-600">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Jam Main
                    </div>
                    <div class="w-2/3 font-semibold text-gray-900">
                        <?php echo date('H:i', strtotime($jadwal->jam_mulai)); ?> - <?php echo date('H:i', strtotime($jadwal->jam_selesai)); ?> WIB
                    </div>
                </div>

                <!-- Total -->
                <div class="flex items-center bg-gradient-to-r from-green-50 to-blue-50 p-4 rounded-lg">
                    <div class="w-1/3 text-gray-700 font-medium">
                        Total Pembayaran
                    </div>
                    <div class="w-2/3 text-3xl font-bold text-green-600">
                        <?php echo mf_format_rupiah($booking->total_harga); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Informasi Penting
            </h3>
            <ul class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-3 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Harap datang <strong>15 menit</strong> sebelum jam main dimulai</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-3 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Tunjukkan <strong>kode booking</strong> ke petugas saat datang</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-3 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Pembayaran dilakukan <strong>di tempat</strong> saat check-in</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-3 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Screenshot halaman ini sebagai bukti booking</span>
                </li>
                <li class="flex items-start">
                    <svg class="w-5 h-5 mr-3 text-red-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Keterlambatan lebih dari <strong>15 menit</strong> akan mengurangi waktu bermain</span>
                </li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 flex-wrap">
            <a href="<?php echo esc_url(home_url('/lapangan/')); ?>" class="flex-1 text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 inline-flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Booking Lagi
            </a>
            <button onclick="window.print()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 inline-flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak
            </button>
        </div>

        <!-- Contact info -->
        <div class="text-center mt-8 text-gray-600">
            <p class="mb-2">Ada pertanyaan? Hubungi kami:</p>
            <div class="flex justify-center gap-6">
                <a href="tel:+6281234567890" class="flex items-center hover:text-green-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    0812-3456-7890
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="flex items-center hover:text-green-600">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path>
                    </svg>
                    WhatsApp
                </a>
            </div>
        </div>

    </div>
</div>

<style media="print">
    header, footer, .bg-green-600, .bg-blue-600, nav { display: none !important; }
    .shadow-lg { box-shadow: none; border: 1px solid #ddd; }
</style>

<?php get_footer(); ?>