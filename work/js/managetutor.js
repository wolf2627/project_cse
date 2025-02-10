$(document).ready(function () {

    console.log('removetutor is ready');

    function loadtutors() {
        $.ajax({
            url: '/api/app/tutor/get/tutors',
            type: 'POST',
            success: function (data) {
                // console.log(data);
                var tutorTableBody = $('#tutor-table tbody');
                tutorTableBody.empty();
                for (var i = 0; i < data.tutors.length; i++) {
                    var tutor = data.tutors[i];
                    var tr = $('<tr></tr>');
                    tr.append('<td>' + tutor.faculty_id + '</td>');
                    tr.append('<td>' + tutor.faculty_name + '</td>');
                    tr.append('<td>' + tutor.department + '</td>');
                    tr.append('<td>' + tutor.batch + '</td>');
                    tr.append('<td>' + tutor.section + '</td>');
                    tr.append('<td><button class="btn btn-danger remove-tutor-btn" data-tutor-id="' + tutor.faculty_id + '"> Unassign </button></td>');
                    tutorTableBody.append(tr);
                }
            },
            error: function (data) {
                console.log(data);
                var errorToast = new Toast('now', 'error', 'Error loading Tutors or no Tutors found');
                errorToast.show();
                $('#tutor-table').hide();
                $('#remove-tutor').empty();
                $('#remove-tutor').append('<p class="alert alert-warning text-center">No Tutors found</p>');
            }
        });
    }


    $(document).on('click', '.remove-tutor-btn', function () {
        var tutor_id = $(this).data('tutor-id');
        console.log('remove-tutor-btn clicked');
        console.log('tutor_id: ' + tutor_id);

        var formdata = new FormData();
        formdata.append('faculty_id', tutor_id);

        $.ajax({
            url: '/api/app/tutor/unassign',
            type: 'POST',
            data: formdata,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                var successToast = new Toast('now', 'success', 'Tutor unassigned successfully');
                successToast.show();
                loadtutors();
            },
            error: function (data) {
                console.log(data);
                var errorToast = new Toast('now', 'error', 'Error unassigning Tutor');
                errorToast.show();
            }
        });

        loadtutors();
    });

    if ($('#remove-tutor-container').length) {
        loadtutors();
    }

});