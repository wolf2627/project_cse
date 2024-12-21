/* global bootstrap: false */
(() => {
    'use strict'
    console.log('sidebar.js loaded')
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipTriggerList.forEach(tooltipTriggerEl => {
        new bootstrap.Tooltip(tooltipTriggerEl)
        console.log('tooltipTriggerEl:', tooltipTriggerEl)
    })
})()