$(document).ready(function () {

    console.log('removeyearincharge is ready');

    function loadyearincharges() {
        $.ajax({
            url: '/api/app/yearincharge/get/yearincharge',
            type: 'POST',
            success: function (data) {
                // console.log(data);
                var yearinchargeTableBody = $('#yearincharge-table tbody');
                yearinchargeTableBody.empty();
                for (var i = 0; i < data.yearincharges.length; i++) {
                    var yearincharge = data.yearincharges[i];
                    var tr = $('<tr></tr>');
                    tr.append('<td>' + yearincharge.faculty_id + '</td>');
                    tr.append('<td>' + yearincharge.faculty_name + '</td>');
                    tr.append('<td>' + yearincharge.department + '</td>');
                    tr.append('<td>' + yearincharge.batch + '</td>');
                    tr.append('<td><button class="btn btn-danger remove-yearincharge-btn" data-yearincharge-id="' + yearincharge.faculty_id + '">Remove</button></td>');
                    yearinchargeTableBody.append(tr);
                }
            },
            error: function (data) {
                console.log(data);
                var errorToast = new Toast('now', 'error', 'Error loading year in charges or no year in charges found');
                errorToast.show();
                $('#yearincharge-table').hide();
                $('#remove-yearincharge').empty();
                $('#remove-yearincharge').append('<p class="alert alert-warning text-center">No year in charges found</p>');
            }
        });
    }


    $(document).on('click', '.remove-yearincharge-btn', function () {
        var yearincharge_id = $(this).data('yearincharge-id');
        console.log('remove-yearincharge-btn clicked');
        console.log('yearincharge_id: ' + yearincharge_id);

        var formdata = new FormData();
        formdata.append('faculty_id', yearincharge_id);

        $.ajax({
            url: '/api/app/yearincharge/remove',
            type: 'POST',
            data: formdata,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                var successToast = new Toast('now', 'success', 'Year in charge removed successfully');
                successToast.show();
                loadyearincharges();
            },
            error: function (data) {
                console.log(data);
                var errorToast = new Toast('now', 'error', 'Error removing year in charge');
                errorToast.show();
            }
        });

        loadyearincharges();
    });

    if ($('#remove-yearincharge-container').length) {
        loadyearincharges();
    }

});