$(document).ready(function () {

    console.log('assignyearincharge is ready');

    $('#assign-yearincharge-btn').click(function (event) {

        console.log('assign-yearincharge-btn clicked');
        event.preventDefault();

        var faculty_id = $('#assign-faculty_id').val();
        var batch = $('#assign-batch').val();
        var department = $('#assign-department').val();

        $('#assign-faculty_id').css('border-color', '');
        $('#assign-batch').css('border-color', '');
        $('#assign-department').css('border-color', '');

        if (faculty_id === '') {
            //highlight the field
            $('#assign-faculty_id').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Faculty ID field');
            errorToast.show();
            return;
        }
        if (department === '') {
            //highlight the field
            $('#assign-department').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Department field');
            errorToast.show();
            return;
        }
        if (batch === '') {
            //highlight the field
            $('#assign-batch').css('border-color', 'red');
            var errorToast = new Toast('now', 'error', 'Please fill the Batch field');
            errorToast.show();
            return;
        }

        $('#assign-faculty_id').css('border-color', 'green');
        $('#assign-batch').css('border-color', 'green');
        $('#assign-department').css('border-color', 'green');


        console.log('faculty_id: ' + faculty_id);
        console.log('batch: ' + batch);
        console.log('department: ' + department);

        var formdata = new FormData();

        formdata.append('faculty_id', faculty_id);
        formdata.append('batch', batch);
        formdata.append('department', department);

        $.ajax({
            url: '/api/app/yearincharge/assign',
            type: 'POST',
            data: formdata,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                if (data.success) {
                    var successToast = new Toast('now', 'success', 'Year Incharge Assigned Successfully');
                    successToast.show();
                } else {
                    var errorToast = new Toast('now', 'error', 'Year Incharge Assign Failed: ' + data.message);
                    errorToast.show();
                }

                document.getElementById('assign-yearincharge-form').reset();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var errorMessage = 'Year Incharge Assign Failed: ' + textStatus;
                if (jqXHR.status === 500) {
                    errorMessage += ' - ' + jqXHR.responseJSON.message;
                    document.getElementById('assign-yearincharge-form').reset();
                } else if (jqXHR.status === 404) {
                    errorMessage += ' - Resource not found';
                    document.getElementById('assign-yearincharge-form').reset();
                }
                var errorToast = new Toast('now', 'error', errorMessage);
                errorToast.show();
            }
        });

        $('#assign-faculty_id').css('border-color', '');
        $('#assign-batch').css('border-color', '');
        $('#assign-department').css('border-color', '');

    });

});