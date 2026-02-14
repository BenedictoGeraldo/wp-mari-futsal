/**
 * Public Script
 * JavaScript untuk frontend booking system
 */

jQuery(document).ready(function ($) {
  "use strict";

  /**
   * Get available slots when date changes
   */
  $("#booking-date").on("change", function () {
    var lapanganId = $("#lapangan-id").val();
    var tanggal = $(this).val();

    if (!lapanganId || !tanggal) {
      return;
    }

    // Show loading
    $("#slot-list").html("<p>Loading...</p>");

    // AJAX request
    $.ajax({
      url: mfPublic.ajaxurl,
      type: "POST",
      data: {
        action: "mf_get_available_slots",
        nonce: mfPublic.nonce,
        lapangan_id: lapanganId,
        tanggal: tanggal,
      },
      success: function (response) {
        if (response.success) {
          // Render available slots
        } else {
          $("#slot-list").html("<p>Error loading slots</p>");
        }
      },
      error: function () {
        $("#slot-list").html("<p>Error connecting to server</p>");
      },
    });
  });

  /**
   * Submit booking form
   */
  $("#booking-form").on("submit", function (e) {
    e.preventDefault();
  });
});
