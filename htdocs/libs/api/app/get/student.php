<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['regNo'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $regNo = $this->_request['regNo'];

        try {
            $student = new Student($regNo);
            $result = $student->getStudentDetails();

            $this->response($this->json($result), 200);
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
