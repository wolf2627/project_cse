// Function to set a cookie
function setCookie(name, value, daysToExpire) {
    var expires = "";

    if (daysToExpire) {
        var date = new Date();
        date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }

    document.cookie = name + "=" + value + expires + "; path=/";
}

$(document).ready(function () {
    // Event listener for 'Sign out' button click
    $('#signOutBtn').on('click', function (e) {
        e.preventDefault(); // Prevent the default link behavior

        // Create a new Dialog instance for logout confirmation
        var d = new Dialog("Sign out Confirmation", "Are you sure you want to sign out?");

        // Set the dialog buttons
        d.setButtons([
            {
                "name": "Sign Out",
                "class": "btn-danger",
                "onClick": function (event) {
                    // Perform the logout action (AJAX request)
                    $.ajax({
                        url: '/api/auth/logout',  // Your API endpoint for logging out
                        type: 'POST',
                        success: function (response) {
                            // On success, redirect to login page
                            window.location.href = '/'; // Or wherever you want to redirect after logout
                        },
                        error: function (error) {
                            // Handle any errors during the logout process
                            alert("Error signing out. Please try again.");
                        }
                    });

                    // Close the dialog after confirming the logout action
                    $(event.data.modal).modal('hide');
                }
            },
            {
                "name": "Cancel",
                "class": "btn-success",
                "onClick": function (event) {
                    // User canceled, just hide the dialog
                    console.log('User canceled sign out.');
                    $(event.data.modal).modal('hide');
                }
            }
        ]);

        // Show the dialog
        d.show();
    });
});



