
$('#class-wise-year-report-btn').click(function () {

    var test_id = this.value;
    var test_name = document.getElementById('test_name_class_wise_year').value;
    var batch = document.getElementById('batch_class_wise_year').value;
    var department = document.getElementById('department_class_wise_year').value;
    var semester = document.getElementById('semester_class_wise_year').value;

    //remove space between words
    test_name = test_name.replace(/\s/g, '');

    var classwiseToast = new Toast("Report", "Now", 'Report is being generated.');
    classwiseToast.show();

    // Send a POST request to the server to generate the PDF
    $.ajax({
        url: '/generate_year_report_classwise',
        type: 'POST',
        data: {
            test_id: test_id
        },
        xhrFields: {
            responseType: 'blob' // Ensure the response is treated as a Blob (binary data)
        },
        success: function (response) {

            var now = new Date();
            var DateTime = now.getFullYear() +
                ('0' + (now.getMonth() + 1)).slice(-2) +
                ('0' + now.getDate()).slice(-2) +
                ('0' + now.getHours()).slice(-2) +
                ('0' + now.getMinutes()).slice(-2) +
                ('0' + now.getSeconds()).slice(-2);

            var successToast = new Toast("Report", "Success", 'Report generated successfully.');
            successToast.show();

            // Create a URL for the blob received in the response
            var fileURL = URL.createObjectURL(response);

            // Create a link element to trigger the download
            var downloadLink = document.createElement('a');
            downloadLink.href = fileURL;
            downloadLink.download = test_name + department + batch + semester + '_Report_' + DateTime + '.pdf'; // Set the default filename
            document.body.appendChild(downloadLink); // Append the link temporarily to the DOM
            downloadLink.click(); // Trigger the click to download
            document.body.removeChild(downloadLink); // Remove the link after the download is triggered
        },
        error: function (xhr, status, error) {
            var errorToast = new Toast("Report", "Error", 'An error occurred while generating the report.');
            errorToast.show();
        }
    });
});


