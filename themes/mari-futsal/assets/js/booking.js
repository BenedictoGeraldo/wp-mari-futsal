(function ($) {
  "use strict";

  $(document).ready(function () {
    let selectedJadwalId = null;
    let hargaPerJam = parseInt($('input[name="harga_per_jam"]').val()) || 0;

    // Handle tanggal change - load available slots
    $("#tanggal-booking").on("change", function () {
      const tanggal = $(this).val();
      const lapanganId = $('input[name="lapangan_id"]').val();

      if (!tanggal) return;

      // Show loading
      $("#loading-slots").removeClass("hidden");
      $("#slot-container").html("");
      selectedJadwalId = null;
      $("#selected-jadwal-id").val("");

      // AJAX get available slots
      $.ajax({
        url: mfBooking.ajaxurl,
        type: "POST",
        data: {
          action: "mf_get_available_slots",
          nonce: mfBooking.nonce,
          lapangan_id: lapanganId,
          tanggal: tanggal,
        },
        success: function (response) {
          $("#loading-slots").addClass("hidden");

          if (response.success) {
            renderSlots(response.data);
          } else {
            showAlert("error", response.data || "Gagal memuat slot waktu");
          }
        },
        error: function () {
          $("#loading-slots").addClass("hidden");
          showAlert("error", "Terjadi kesalahan sistem");
        },
      });
    });

    // Render slots
    function renderSlots(slots) {
      const container = $("#slot-container");
      container.html("");

      if (slots.length === 0) {
        container.html(
          '<p class="text-gray-500 text-center py-6 col-span-full">Tidak ada slot tersedia</p>',
        );
        return;
      }

      slots.forEach(function (slot) {
        const slotItem = $("<div>")
          .addClass(
            "border-2 rounded-lg p-3 text-center cursor-pointer transition-all duration-200",
          )
          .attr("data-jadwal-id", slot.id);

        if (slot.available) {
          slotItem
            .addClass(
              "border-gray-300 hover:border-green-600 hover:bg-green-50",
            )
            .html(
              `
                            <div class="font-semibold text-gray-900">${slot.jam_mulai}</div>
                            <div class="text-xs text-gray-600">${slot.jam_selesai}</div>
                        `,
            )
            .on("click", function () {
              selectSlot($(this));
            });
        } else {
          slotItem.addClass(
            "border-gray-200 bg-gray-100 cursor-not-allowed opacity-50",
          ).html(`
                            <div class="font-semibold text-gray-500">${slot.jam_mulai}</div>
                            <div class="text-xs text-gray-500">Booked</div>
                        `);
        }

        container.append(slotItem);
      });
    }

    // Select slot
    function selectSlot($slot) {
      $("#slot-container > div").removeClass(
        "border-green-600 bg-green-600 text-white",
      );
      $("#slot-container > div").addClass("border-gray-300");
      $("#slot-container > div div")
        .removeClass("text-white")
        .addClass("text-gray-900");

      $slot.removeClass("border-gray-300 hover:bg-green-50");
      $slot.addClass("border-green-600 bg-green-600");
      $slot
        .find("div")
        .removeClass("text-gray-900 text-gray-600")
        .addClass("text-white");

      selectedJadwalId = $slot.data("jadwal-id");
      $("#selected-jadwal-id").val(selectedJadwalId);
    }

    // Format rupiah
    function formatRupiah(angka) {
      return "Rp " + parseInt(angka).toLocaleString("id-ID");
    }

    // Handle form submit
    $("#mf-booking-form").on("submit", function (e) {
      e.preventDefault();

      // Validation
      if (!selectedJadwalId) {
        showAlert("error", "Silakan pilih jam main terlebih dahulu");
        return;
      }

      const formData = {
        action: "mf_submit_booking",
        nonce: mfBooking.nonce,
        lapangan_id: $('input[name="lapangan_id"]').val(),
        jadwal_id: selectedJadwalId,
        tanggal: $("#tanggal-booking").val(),
        nama: $("#nama-customer").val().trim(),
        no_hp: $("#no-hp-customer").val().trim(),
        total_harga: hargaPerJam,
      };

      // Validate phone number
      const phonePattern = /^[0-9]{10,13}$/;
      if (!phonePattern.test(formData.no_hp)) {
        showAlert("error", "Nomor HP tidak valid. Harus 10-13 digit angka.");
        return;
      }

      // Disable submit button
      const $btnSubmit = $("#btn-submit-booking");
      const originalHtml = $btnSubmit.html();
      $btnSubmit.prop("disabled", true).html(`
                <svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
            `);

      // Submit via AJAX
      $.ajax({
        url: mfBooking.ajaxurl,
        type: "POST",
        data: formData,
        success: function (response) {
          if (response.success) {
            // Redirect to konfirmasi page
            const kodeBooking = response.data.kode_booking;
            window.location.href =
              mfBooking.homeurl + "/konfirmasi/?kode=" + kodeBooking;
          } else {
            showAlert("error", response.data || "Gagal melakukan booking");
            $btnSubmit.prop("disabled", false).html(originalHtml);
          }
        },
        error: function () {
          showAlert("error", "Terjadi kesalahan sistem. Silakan coba lagi.");
          $btnSubmit.prop("disabled", false).html(originalHtml);
        },
      });
    });

    // Show alert
    function showAlert(type, message) {
      const alertClass =
        type === "error"
          ? "bg-red-100 border-red-400 text-red-700"
          : "bg-green-100 border-green-400 text-green-700";
      const icon =
        type === "error"
          ? '<svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>'
          : '<svg class="w-5 h-5 mr-2 inline" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';

      const alert = $("<div>")
        .addClass("border px-4 py-3 rounded mb-4 " + alertClass)
        .html(icon + message);

      $("#mf-alert-container").html(alert);

      // Scroll to alert
      $("html, body").animate(
        {
          scrollTop: $("#mf-alert-container").offset().top - 100,
        },
        500,
      );

      // Auto hide after 5 seconds
      setTimeout(function () {
        alert.fadeOut(function () {
          $(this).remove();
        });
      }, 5000);
    }
  });
})(jQuery);
