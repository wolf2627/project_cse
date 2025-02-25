<?php


${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists('facultyId')) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $facultyId = $this->_request['facultyId'];

        try {

            $att = new Attendance();

            $result = $att->getMarkedSessions($facultyId);

            if ($result) {
                $this->response($this->json([
                    'success' => true,
                    'message' => $result
                ]), 200);
            } else {
                $this->response($this->json([
                    'success' => false,
                    'message' => 'No attendance marked'
                ]), 404);
            }
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
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};
