/* global bootstrap: false */
(() => {
  'use strict'
  const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
  tooltipTriggerList.forEach(tooltipTriggerEl => {
    new bootstrap.Tooltip(tooltipTriggerEl)
  })
})()



// // JavaScript to toggle the sidebar visibility on mobile devices
// $(document).ready(function () {
//   const sidebar = document.querySelector('.sidebar-cus');
//   const toggleButton = document.querySelector('.sidebar-expand'); // Make sure you have a button or element to toggle the sidebar

//   // Listen for click event to toggle sidebar visibility
//   toggleButton.on('click', function () {
//     sidebar.classList.toggle('visible'); // Toggle the "visible" class to expand/collapse the sidebar
//   });

//   // Close the sidebar if clicking outside of it (optional)
//   document.addEventListener('click', function (event) {
//     if (!sidebar.contains(event.target) && !toggleButton.contains(event.target)) {
//       sidebar.classList.remove('visible'); // Hide the sidebar if clicked outside
//     }
//   });
// });



// const sidebar = document.querySelector('.sidebar-cus');
// const overlay = document.querySelector('.sidebar-overlay');
// const toggleButton = document.querySelector('.btn-collapse');

// toggleButton.on('click', () => {
//   sidebar.classList.toggle('visible');
//   overlay.classList.toggle('visible');
// });

$(document).ready(function () {
  const $sidebar = $('.sidebar-cus'); // Use jQuery for consistency
  const $toggleButton = $('.sidebar-expand'); // Button to toggle the sidebar
  const $overlay = $('.sidebar-overlay'); // Optional overlay for the sidebar

  // Listen for click event to toggle sidebar visibility
  $toggleButton.click(function () {
    $sidebar.toggleClass('visible');
    $overlay.toggleClass('visible'); // Toggle overlay visibility if needed
  });

  // Close the sidebar if clicking outside of it (optional)
  $(document).click(function (event) {
    if (
      !$sidebar.is(event.target) &&
      !$sidebar.has(event.target).length &&
      !$toggleButton.is(event.target) &&
      !$toggleButton.has(event.target).length
    ) {
      $sidebar.removeClass('visible');
      $overlay.removeClass('visible'); // Hide overlay if needed
    }
  });

  // Prevent closing the sidebar when clicking inside it
  $sidebar.click(function (event) {
    event.stopPropagation();
  });

  // Prevent closing the sidebar when clicking the toggle button
  $toggleButton.click(function (event) {
    event.stopPropagation();
  });
});
