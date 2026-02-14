/**
 * Admin Script
 * event handlers, validation, and ui interactions
 */
jQuery(document).ready(function ($) {
  "use strict";

  $(document).on("click", ".mf-delete-btn", function (e) {
    const itemName = $(this).data("item-name") || "data ini";
    const confirmMsg = `Apakah  anda yakin ingin menghapus ${itemName}?\n\nTindakan ini tidak dapat dibatalkan!`;

    if (!confirm(confirmMsg)) {
      e.preventDefault();
      return false;
    }
  });

  $(".mf-form").on("submit", function (e) {
    const form = $(this);
    let hasError = false;

    form.find(".error-message").remove();
    form.find(".has-error").removeClass("has-error");

    form.find("[required]").each(function () {
      const field = $(this);
      const value = field.val();

      if (!value || value.trim() === "") {
        hasError = true;
        const formGroup = field.closest(".mf-form-group");
        formGroup.addClass("has-error");

        const label = formGroup.find("label").text().replace("*", "").trim();
        formGroup.append(
          `<span class="error-message">${label} wajib diisi</span>`,
        );
      }
    });

    form.find('input[type="number"]').each(function () {
      const field = $(this);
      const value = field.val();

      if (value && isNaN(value)) {
        hasError = true;
        const formGroup = field.closest(".mf-form-group");
        formGroup.addClass("has-error");
        formGroup.append(
          `<span class="error-message">harus berupa angka</span>`,
        );
      }
    });

    if (hasError) {
      e.preventDefault();
      $("html, body").animate(
        {
          scrollTop: $(".has-error").first().offset().top - 100,
        },
        300,
      );
      return false;
    }

    form.addClass("mf-loading");
  });

  $(document).on("change", ".mf-image-upload", function (e) {
    const file = e.target.files[0];
    const preview = $(this).closest(".mf-form-group").find(".mf-image-preview");

    if (file) {
      if (!file.type.startsWith("image/")) {
        alert("File harus berupa gambar (JPG, PNG, GIF");
        $(this).val("");
        return;
      }

      if (file.size > 2 * 1024 * 1024) {
        alert("ukuran file maksimal 2MB");
        $(this).val("");
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        preview.html(
          `<img src="${e.target.result}" style="max-width: 300px; max-height: 300px; border-radius: 6px; margin-top:10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"/> `,
        );
      };
      reader.readAsDataURL(file);
    }
  });

  window.mfShowLoading = function (element) {
    $(element).removeClass("mf-loading");
  };

  window.mfHideLoading = function (element) {
    $(element).removeClass("mf-loading");
  };

  setTimeout(function () {
    $(".notice.is.dismissible").fadeOut(400, function () {
      $(this).remove();
    });
  }, 5000);

  function makeTablesResponsive() {
    $(".mf-table-wrapper").each(function () {
      const wrapper = $(this);
      const table = wrapper.find("table");

      if (table.length && table.width() > wrapper.width()) {
        wrapper.css({
          "overflow-x": "auto",
          "-webkit-overflow-scrolling": "touch",
        });
      }
    });
  }

  makeTablesResponsive();
  $(window).on("resize", makeTablesResponsive);

  $('a[href^="#"]').on("click", function (e) {
    const target = $(this.hash);
    if (target.length) {
      e.preventDefault();
      $("html, body").animate(
        {
          scrollTop: target.offset().top - 100,
        },
        500,
      );
    }
  });

  $('input[type="number"]').on("keypress", function (e) {
    if (
      $.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
      (e.keyCode === 65 && e.ctrlKey === true) ||
      (e.keyCode === 67 && e.ctrlKey === true) ||
      (e.keyCode === 86 && e.ctrlKey === true) ||
      (e.keyCode === 88 && e.ctrlKey === true) ||
      (e.keyCode >= 35 && e.keyCode <= 39)
    ) {
      return;
    }

    if (
      (e.shiftKey || e.keyCode < 48 || e.keyCOde > 57) &&
      (e.keyCode < 96 || e.keyCode > 105)
    ) {
      e.preventDefault();
    }
  });

  $("[title]").each(function () {
    const title = $(this).attr("title");
    if (title) {
      $(this).attr("data-tooltip", title);
    }
  });

  $(".mf-btn:not(.mf-btn-disabled)").on("click", function () {
    const btn = $(this);
    btn.addClass("mf-btn-clicked");
    setTimeOut(function () {
      btn.removeClass("mf-btn-clicked");
    }, 150);
  });

  $(".mf-form-group input, .mf-form-group select, .mf-form-group textarea").on(
    "input change",
    function () {
      const formGroup = $(this).closest(".mf-form-group");
      if (formGroup.hasClass("has-error")) {
        formGroup.removeClass("has-error");
        formGroup.find(".error-message").fadeOut(200, function () {
          $(this).remove();
        });
      }
    },
  );

  // ========================================
  // TOGGLE FORM ADD/EDIT LAPANGAN
  // ========================================
  $("#btn-toggle-form").on("click", function () {
    const formContainer = $("#form-container");
    const btn = $(this);

    if (formContainer.is(":visible")) {
      formContainer.slideUp(300);
      btn.html(
        '<span class="dashicons dashicons-plus-alt"></span> Tambah Lapangan',
      );
    } else {
      formContainer.slideDown(300);
      btn.html('<span class="dashicons dashicons-no-alt"></span> Tutup Form');
      // Focus on first input
      setTimeout(function () {
        $("#nama").focus();
      }, 350);
    }
  });

  // Close form with ESC key
  $(document).on("keyup", function (e) {
    if (e.key === "Escape") {
      const formContainer = $("#form-container");
      if (formContainer.is(":visible")) {
        formContainer.slideUp(300);
        $("#btn-toggle-form").html(
          '<span class="dashicons dashicons-plus-alt"></span> Tambah Lapangan',
        );
      }
    }
  });

  // ========================================
  // FORMAT HARGA REAL-TIME
  // ========================================
  $("#harga").on("input", function () {
    const value = $(this).val();
    const numValue = parseInt(value.replace(/\D/g, ""));

    if (!isNaN(numValue)) {
      // Format number dengan thousand separator
      const formatted = numValue.toLocaleString("id-ID");
      // Tampilkan preview formatted (opsional, bisa juga langsung format input)
      // Untuk sekarang kita skip format langsung di input karena bisa mengganggu editing
    }
  });

  // ========================================
  // ENHANCED DELETE CONFIRMATION
  // ========================================
  $(document).on("click", ".mf-delete-btn", function (e) {
    e.preventDefault();

    const itemName = $(this).data("item-name") || "data ini";
    const deleteUrl = $(this).attr("href");

    const confirmMsg =
      `Apakah Anda yakin ingin menghapus lapangan "${itemName}"?\n\n` +
      "⚠️ PERINGATAN:\n" +
      "- Data yang dihapus tidak dapat dikembalikan!\n" +
      "- Foto lapangan akan terhapus dari server.\n" +
      "- Jika lapangan memiliki booking aktif, penghapusan akan ditolak.\n\n" +
      "Ketik 'HAPUS' untuk konfirmasi.";

    const userInput = prompt(confirmMsg);

    if (userInput === "HAPUS") {
      // Show loading indicator
      $("body").append(
        '<div class="mf-loading-overlay"><div class="mf-spinner"></div><p>Menghapus data...</p></div>',
      );

      // Redirect to delete URL
      window.location.href = deleteUrl;
    }
  });

  // ========================================
  // IMAGE PREVIEW ENHANCEMENT
  // ========================================
  $(document).on("change", "#foto", function (e) {
    const file = e.target.files[0];
    const preview = $(".mf-image-preview");

    // Clear previous preview
    preview.empty();

    if (file) {
      // Validate file type
      const validTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif"];
      if (!validTypes.includes(file.type)) {
        alert("❌ File harus berupa gambar (JPG, PNG, GIF)");
        $(this).val("");
        return;
      }

      // Validate file size (max 2MB)
      const maxSize = 2 * 1024 * 1024; // 2MB
      if (file.size > maxSize) {
        const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
        alert(`❌ Ukuran file terlalu besar (${sizeMB} MB).\nMaksimal 2 MB.`);
        $(this).val("");
        return;
      }

      // Show preview
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.html(`
          <div style="margin-top: 15px; padding: 15px; background: #f0f0f1; border-radius: 6px;">
            <p style="margin: 0 0 10px 0; font-weight: 600;">Preview:</p>
            <img src="${e.target.result}" 
                 style="max-width: 300px; max-height: 300px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); display: block;">
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #666;">
              <strong>Nama file:</strong> ${file.name}<br>
              <strong>Ukuran:</strong> ${(file.size / 1024).toFixed(2)} KB
            </p>
          </div>
        `);
      };
      reader.readAsDataURL(file);
    }
  });

  // ========================================
  // FORM CANCEL BUTTON
  // ========================================
  // Already handled by link, no additional JS needed

  // ========================================
  // AUTO SCROLL TO FORM IF EDIT MODE
  // ========================================
  if ($("#form-container").is(":visible")) {
    // Edit mode detected
    $("#btn-toggle-form").html(
      '<span class="dashicons dashicons-no-alt"></span> Tutup Form',
    );

    $("html, body").animate(
      {
        scrollTop: $("#form-container").offset().top - 100,
      },
      500,
    );
  }

  // ========================================
  // PREVENT DOUBLE SUBMIT
  // ========================================
  $(".mf-form").on("submit", function () {
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop("disabled", true);
    submitBtn.html(
      '<span class="dashicons dashicons-update rotating"></span> Menyimpan...',
    );

    // Add CSS for rotating animation
    if (!$("#rotating-animation").length) {
      $("head").append(`
        <style id="rotating-animation">
          .rotating {
            animation: rotate 1s linear infinite;
          }
          @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
          }
        </style>
      `);
    }
  });

  // ========================================
  // JADWAL (SLOT WAKTU) FEATURES
  // ========================================

  // Time Input Validation - Real-time feedback
  $('input[type="time"]').on("change", function () {
    const jamMulai = $("#jam_mulai").val();
    const jamSelesai = $("#jam_selesai").val();

    if (jamMulai && jamSelesai) {
      const start = new Date("2000-01-01 " + jamMulai);
      const end = new Date("2000-01-01 " + jamSelesai);

      // Remove previous warnings
      $(".time-validation-warning").remove();

      if (end <= start) {
        $("#jam_selesai").after(
          '<span class="time-validation-warning" style="display: block; margin-top: 8px; padding: 8px 12px; color: #721c24; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px;">' +
            "⚠️ Jam selesai harus lebih besar dari jam mulai!</span>",
        );
        $(this)
          .closest("tr")
          .find('input[type="time"]')
          .css("border-color", "#d63638");
      } else {
        $(this)
          .closest("tr")
          .find('input[type="time"]')
          .css("border-color", "#00a32a");

        // Calculate duration
        const duration = Math.round((end - start) / 60000);
        $("#jam_selesai").after(
          '<span class="time-validation-warning" style="display: block; margin-top: 8px; padding: 8px 12px; color: #155724; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px;">' +
            "✅ Durasi: " +
            duration +
            " menit (" +
            (duration / 60).toFixed(1) +
            " jam)</span>",
        );
      }
    }
  });

  // Delete confirmation for jadwal (enhanced)
  $(document).on("click", ".button-link-delete", function (e) {
    const confirmMsg =
      "⚠️ KONFIRMASI PENGHAPUSAN\n\n" +
      "Apakah Anda yakin ingin menghapus slot waktu ini?\n\n" +
      "⚠️ PERHATIAN:\n" +
      "• Data yang dihapus tidak dapat dikembalikan\n" +
      "• Jika slot ini memiliki booking aktif, penghapusan akan ditolak\n\n" +
      'Klik "OK" untuk melanjutkan atau "Batal" untuk membatalkan.';

    if (!confirm(confirmMsg)) {
      e.preventDefault();
      return false;
    }
  });

  // Smooth scroll to form when add/edit button clicked
  $('a[href*="form="]').on("click", function () {
    setTimeout(function () {
      if ($(".mf-card").length) {
        $("html, body").animate(
          {
            scrollTop: $(".mf-card").first().offset().top - 100,
          },
          400,
        );
      }
    }, 100);
  });
});
