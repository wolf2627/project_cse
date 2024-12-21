$('#generateExcel-btn').click(function () {
    var formData = new FormData();
    var batch = $('#batch').val();
    var semester = $('#semester').val();
    var section = $('#section').val();
    var subject = $('#subject_code').val();
    var department = $('#department').val();
    var test_name = $('#test_name').val();
    var faculty_id = $('#faculty_id').val();

    // Append data
    formData.append('batch', batch);
    formData.append('semester', semester);
    formData.append('section', section);
    formData.append('subject_code', subject);
    formData.append('department', department);
    formData.append('test_name', test_name);
    formData.append('faculty_id', faculty_id);


    console.log(batch, semester, section, subject, department, test_name, faculty_id);

    console.log(formData);

    // Send the request
    $.ajax({
        url: '/generate_class_sub_excel', // Your endpoint
        type: 'POST',
        data: formData,
        processData: false, // Prevent jQuery from converting the data
        contentType: false, // Let the browser set the content type for FormData
        xhrFields: {
            responseType: 'blob' // Important to handle binary response
        },
        success: function (response) {
            // Create a URL for the blob (Excel file)
            var blob = response;
            var url = window.URL.createObjectURL(blob);

            // Create an anchor element for download
            var a = document.createElement('a');
            a.href = url;
            a.download = 'report_' + batch + '_' + semester + '_' + section + '_' + subject + '_' + department + '_' + test_name + '.xlsx'; // Set the download filename

            // Trigger the download
            a.click();

            // Clean up the URL object
            window.URL.revokeObjectURL(url);
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
        }
    });
});
