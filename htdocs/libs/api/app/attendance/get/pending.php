<?php

// This Api is used to get the pending attendance for a faculty

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['faculty_id'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $faculty_id = $this->_request['faculty_id'];

        $att = new Attendance();

        try {
            $result = $att->getPendingAttendance($faculty_id);

            $this->response(
                $this->json([
                    'success' => true,
                    'pending' => $result
                ]),
                200
            );
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/\b(?:not|no)\b/i', $errorMessage)) {
                $this->response($this->json([
                    'success' => false,
                    'message' => $errorMessage
                ]), 404);
            } else {
                $this->response($this->json([
                    'success' => false,
                    'message' => $errorMessage
                ]), 500);
            }
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
