/**
 * Admin Script
 * event handlers, validation, and ui interactions
 */
jQuery(document).ready(function ($) {
  "use strict";

  console.log("Mari Futsal Admin Script");
  console.log("AJAX URL:", mfAjax.ajaxurl);

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

  console.e.log("admin skrip telah sukses diinisialisasi");
});
