<?php

// https://domain/api/app/create/assignfaculty

${basename(__FILE__, '.php')} = function () {
    // Required parameters
    $requiredParams = ['faculty_id', 'subject_code', 'batch', 'department', 'semester', 'section', 'student_sections', 'year'];

    // Check if required parameters exist
    if ($this->paramsExists($requiredParams)) {
        
        // Ensure the user is authenticated
        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        error_log('Assigning faculty operation initiated');

        // Extract parameters from request
        $faculty_id = $this->_request['faculty_id'];
        $subject_code = $this->_request['subject_code'];
        $batch = $this->_request['batch'];
        $department = $this->_request['department'];
        $semester = $this->_request['semester'];
        $section = $this->_request['section'];
        $student_sections = $this->_request['student_sections'];
        $year = $this->_request['year'];

        // Call the core logic to assign faculty
        $enroll_result = Creator::assignFaculty($faculty_id, $subject_code, $batch, $department, $semester, $section, $student_sections, $year);

        // Prepare and send the response
        if ($enroll_result) {
            $this->response($this->json(['message' => $enroll_result]), 200);
        } else {
            $this->response($this->json(['message' => 'Assignment failed']), 500);
        }
    } else {
        // Missing parameters
        $this->response($this->json(['message' => 'Bad request: Missing required parameters']), 400);
    }
};

