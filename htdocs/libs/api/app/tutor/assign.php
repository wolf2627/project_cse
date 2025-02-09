<?php

${basename(__FILE__, '.php')} = function () {

    $requiredParams = ['faculty_id', 'department', 'batch', 'section'];

    if ($this->paramsExists($requiredParams)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }


        $faculty_id = $this->_request['faculty_id'];
        $department = $this->_request['department'];
        $batch = $this->_request['batch'];
        $section = $this->_request['section'];

        error_log("Assigning year in charge for $faculty_id");

        try {
            $assigned = Tutor::assignTutor($faculty_id, $department, $batch, $section);
            if ($assigned) {
                $this->response($this->json([
                'success' => true,
                'message' => 'Tutor assigned']), 200);
            } else {
                $this->response($this->json([
                    'success' => false,
                    'message' => 'Assignment failed']), 500);
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
