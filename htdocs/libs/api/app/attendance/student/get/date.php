<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['student_id'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $student_id = $this->_request['student_id'];
        // date is optional
        $date = $this->paramsExists(['date']) ? $this->_request['date'] : null;
        try {

            $att = new Attendance();
            $result = $att->calculateAttendanceByDate($student_id, $date);

            if (empty($result)) {
                $this->response($this->json([
                    'success' => false,
                    'message' => 'No attendance marked'
                ]), 404);
            } else {
                $this->response($this->json([
                    'success' => true,
                    'message' => $result
                ]), 200);
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
