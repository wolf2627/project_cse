<?php

${basename(__FILE__, '.php')} = function () {

    // $batch, $semester, $subject_code, $section, $department, $faculty_id
    if ($this->paramsExists(['batch', 'semester', 'subject_code', 'section', 'department', 'faculty_id'])) {


        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $batch = $this->_request['batch'];
        $semester = $this->_request['semester'];
        $subject_code = $this->_request['subject_code'];
        $section = $this->_request['section'];
        $department = $this->_request['department'];
        $faculty_id = $this->_request['faculty_id'];



        $result = Classes::getClassId($batch, $semester, $subject_code, $section, $department, $faculty_id);

        if (!$result) {
            $this->response(
                $this->json([
                    'success' => false,
                    'message' => 'No classes found.'
                ]),
                404 // Internal Server Error
            );
        }

        $this->response(
            $this->json([
                'success' => true,
                'class_id' => $result
            ]),
            200
        );
    } else {
        // Bad request response Missing Parameters
        $this->response(
            $this->json([
                'success' => false,
                'message' => 'Bad request'
            ]),
            400 // Bad Request
        );
    }
};
