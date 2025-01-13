<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists('faculty_id', 'class_id', 'date')) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $faculty_id = $this->_request['faculty_id'];
        $class_id = $this->_request['class_id'];
        $date = $this->_request['date'];

        try {
            $att = new Attendance();
            $attData = $att->getMarkedFacultyAttendance($faculty_id, $class_id, $date);

            $this->response($this->json([
                'success' => true,
                'message' => $attData
            ]), 200);
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