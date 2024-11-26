<?php

// https://domain/api/app/update/updatemark

${basename(__FILE__, '.php')} = function () {
    // Required parameters
    $requiredParams = ['reg_no', 'new_mark', 'batch', 'semester', 'subject_code', 'testname', 'section', 'department'];

    // Check if required parameters exist
    if ($this->paramsExists($requiredParams)) {

        // Ensure the user is authenticated
        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $faculty = new Faculty();

        // Extract parameters from request
        $reg_no = $this->_request['reg_no'];
        $new_mark = intval($this->_request['new_mark']);
        $batch = $this->_request['batch'];
        $semester = $this->_request['semester'];
        $subject_code = $this->_request['subject_code'];
        $testname = $this->_request['testname'];
        $section = $this->_request['section'];
        $department = $this->_request['department'];

        // Update marks
        error_log("Updating marks for $reg_no");
        
        error_log("New record: $reg_no, $new_mark, $batch, $semester, $subject_code, $testname, $section, $department");

        $updated = Marks::updateMarks($reg_no, $new_mark, $batch, $semester, $subject_code, $testname, $section, $department, $faculty->getFacultyId());

        // Prepare and send the response
        if ($updated) {
            $this->response($this->json(['message' => "mark updated: $new_mark"]), 200);
        } else {
            $this->response($this->json(['message' => 'Assignment failed']), 500);
        }
    } else {
        // Missing parameters
        $this->response($this->json(['message' => 'Bad request: Missing required parameters']), 400);
    }
};
