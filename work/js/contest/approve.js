$(document).ready(function () {
    console.log("Approve script loaded [new loaded] ");

    $(".approve-btn").click(function () {
        console.log("Approve button clicked");
        let button = $(this);
        let contestId = button.data("contest-id");
        let studentId = button.data("student-id");

        let pendingCountElement = $("#pending-count");
        let pendingCount = parseInt(pendingCountElement.data("pending-count"));

        console.log("pendingCount: " + pendingCount);

        console.log("Approving registration for student " + studentId + " in contest " + contestId);

        // Show loading state
        button.prop("disabled", true).html('<i class="fas fa-spinner fa-spin"></i> Approving...');

        $.ajax({
            url: "/api/contest/confirmregistration",
            type: "POST",
            data: { contestId: contestId, studentId: studentId },
            success: function (response) {
                $("#status-" + studentId)
                    .removeClass("bg-secondary")
                    .addClass("bg-success")
                    .text("Approved");


                button.replaceWith('<span class="text-success">Approved</span>');

                // Show success message
                var toast = new Toast("now", "success", "Registration approved successfully");
                toast.show();
                pendingCount = pendingCount - 1;
                pendingCountElement.data("pending-count", pendingCount);
                pendingCountElement.text(pendingCount + " Pending");


            },
            error: function () {
                alert("An error occurred while processing the request.");
                button.prop("disabled", false).html('<i class="fas fa-check"></i> Approve');
            }
        });
    });
});