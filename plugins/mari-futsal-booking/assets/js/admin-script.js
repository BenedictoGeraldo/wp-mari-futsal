/**
 * Admin Script
 * JavaScript untuk admin pages
 */

jQuery(document).ready(function ($) {
  "use strict";

  console.log("Mari Futsal Admin Script Loaded");
  console.log("AJAX URL:", mfAjax.ajaxurl);

  // Will be developed in Day 2-4:
  // - Form handling
  // - AJAX requests
  // - Delete confirmations
  // - Image upload preview
  // - Form validation

  /**
   * Delete confirmation
   */
  $(document).on("click", ".mf-delete-btn", function (e) {
    if (!confirm("Apakah Anda yakin ingin menghapus data ini?")) {
      e.preventDefault();
      return false;
    }
  });

  /**
   * Form validation placeholder
   */
  $(".mf-form").on("submit", function (e) {
    // Will add validation in Day 3-4
    console.log("Form submitted");
  });
});
