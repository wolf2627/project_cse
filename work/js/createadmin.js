$(document).ready(function () {

    console.log('createadmin is ready');

    $('#admin-create-account-btn').click(function () {
        console.log('create admin account clicked');
        var username = $('#admin-username').val();
        var password = $('#admin-password').val();
        var email = $('#admin-email').val();
        var confirm_password = $('#admin-confirm-password').val();

        console.log('username: ' + username);
        console.log('password: ' + password);
        console.log('email: ' + email);
        console.log('confirm_password: ' + confirm_password);

        if (password !== confirm_password) {
            alert('Passwords do not match');
            return;
        }

        var formData = new FormData();
        formData.append('user', username);
        formData.append('password', password);
        formData.append('email', email);
        formData.append('confirm_password', confirm_password);

        $.ajax({
            url: '/api/app/create/admin',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log('create admin account success');
                console.log(data);
                if (data.message === 'success') {
                    var successToast = new Toast('now', 'success', 'Admin account created successfully');
                    successToast.show();
                } else {
                    var errorToast = new Toast('now', 'error', 'Error creating admin account');
                    errorToast.show();
                }
            },
            error: function (err) {
                console.log('create admin account error');
                console.log(err);
                var errorToast = new Toast('now', 'error', 'Error creating admin account');
                errorToast.show();
            }
        });


    });

});