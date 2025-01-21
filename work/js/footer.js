$(document).ready(function () {
    setInterval(function () {
        var text1 = $('#footer-text-1');
        var text2 = $('#footer-text-2');

        if (text1.is(':visible')) {
            text1.fadeOut('slow', function () {
                text2.fadeIn('slow');
            });
        } else {
            text2.fadeOut('slow', function () {
                text1.fadeIn('slow');
            });
        }
    }, 5000);
});