<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['subject_code', 'faculty_id', 'batch', 'semester'])) {


        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $subject_code = $this->_request['subject_code'];
        $faculty_id = $this->_request['faculty_id'];
        $batch = $this->_request['batch'];
        $semester = $this->_request['semester'];


        $result = Classes::getClass($subject_code, $faculty_id, $batch, $semester);

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
                'classes' => $result
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
