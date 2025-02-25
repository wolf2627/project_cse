<?php

// This API returns the list of students assigned to a faculty for a particular subject.

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['subjectCode', 'batch', 'semester', 'section'])) {
        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $subjectCode = $this->_request['subjectCode'];
        $batch = $this->_request['batch'];
        $semester = $this->_request['semester'];
        $section = $this->_request['section'];

        if (isset($this->_request['facultyId'])) {
            $facultyId = $this->_request['facultyId'];
        } else {
            $facultyId = null;
        }

        try {
            $faculty = new Faculty();
            $result = $faculty->getAssignedStudents($subjectCode, $batch, $semester, $section, $facultyId);

            if(!$result) {
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => 'No students found.'
                    ]),
                    404 // Not Found
                );
            }

            $this->response($this->json([
                'success' => true,
                'students' => $result
            ]), 200);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/\b(?:not|no)\b/i', $errorMessage)) {
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]),
                    404 // Internal Server Error
                );
            } else {
                $this->response(
                    $this->json([
                        'success' => false,
                        'message' => $errorMessage
                    ]),
                    500
                );
            }
        }

        $this->response($this->json($result), 200);
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
