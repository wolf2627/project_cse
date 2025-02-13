$(document).ready(function () {
    console.log("Register script loaded successfully");
    $(".register-btn").click(function () {
        var contestId = $(this).data("contest-id");
        var button = $(this);
        button.prop("disabled", true);
        button.html("Registering...");

        $.ajax({
            url: "/api/contest/register",
            type: "POST",
            data: { contestId: contestId },
            success: function (response) {
                var toast = new Toast("now", "success", "Registration successful");
                toast.show();
                button.html("Registered");
            },
            error: function (xhr, status, error) {
                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : "Registration failed";
                var toast = new Toast("now", "error", errorMessage + " Please try again later.");
                toast.show();
                button.html("Failed");
                button.prop("disabled", false);
            }
        });
    });
});
