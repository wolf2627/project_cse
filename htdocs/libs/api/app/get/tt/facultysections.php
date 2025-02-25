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
        try {
            $faculty = new Faculty($faculty_id);
            $result = $faculty->getSections($subject_code, $batch, $semester, $faculty_id);

            if (!$result) {
            $this->response(
                $this->json([
                'success' => false,
                'message' => 'No Sections found.'
                ]),
                404 // Not Found
            );
            } else {
            $this->response(
                $this->json([
                'success' => true,
                'Sections' => $result
                ]),
                200
            );
            }
        } catch (Exception $e) {
            $this->response(
            $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]),
            500 // Internal Server Error
            );
        }
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
