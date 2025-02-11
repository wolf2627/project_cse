$(document).ready(function () {
    console.log("viewstudentdetails-tutor.js loaded");

    $('.view-student-detail-tutor').on('click', function () {
        var studentId = $(this).data('student_id');
        console.log("Student ID: " + studentId);

        $.ajax({
            url: '/api/app/student/details',
            type: 'POST',
            data: {
                student_id: studentId
            },
            success: function (response) {
                var studentDetails = response.studentDetails;
                var enrolledClasses = response.enrolledClasses;

                var detailsHtml = `
    <div class="container">
        <h3 class="mb-3 text-primary">Student Details</h3>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Name:</strong> ${studentDetails.name}</p>
                <p><strong>Registration No:</strong> ${studentDetails.reg_no}</p>
                <p><strong>Email:</strong> ${studentDetails.email}</p>
                <p><strong>Roll No:</strong> ${studentDetails.roll_no}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Semester:</strong> ${studentDetails.semester}</p>
                <p><strong>Section:</strong> ${studentDetails.section}</p>
            </div>
        </div>

        <h3 class="mt-4 mb-3 text-primary">Enrolled Classes</h3>
        <div class="list-group">
`;

                enrolledClasses.forEach(function (classInfo) {
                    detailsHtml += `
        <div class="list-group-item">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Faculty:</strong> ${classInfo.faculty}</p>
                    <p><strong>Department:</strong> ${classInfo.department}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Subject Code:</strong> ${classInfo.subject_code}</p>
                    <p><strong>Semester:</strong> ${classInfo.semester}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Section:</strong> ${classInfo.section}</p>
                     <p><strong>Batch:</strong> ${classInfo.batch}</p>
                </div>
            </div>
        </div>
    `;
                });

                detailsHtml += `</div></div>`;


                var d = new Dialog("Student Details ("+ studentId + ")" , detailsHtml, {'size': 'large'});
                d.show();
            }
        });


    });
});
