<?php

${basename(__FILE__, '.php')} = function () {

    $requiredParams = ['faculty_id', 'department', 'batch'];

    if ($this->paramsExists($requiredParams)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }


        $faculty_id = $this->_request['faculty_id'];
        $department = $this->_request['department'];
        $batch = $this->_request['batch'];

        error_log("Assigning year in charge for $faculty_id");

        try {
            $assigned = YearInCharge::assignYearInCharge($faculty_id, $department, $batch);
            if ($assigned) {
                $this->response($this->json(['message' => 'Year in charge assigned']), 200);
            } else {
                $this->response($this->json(['message' => 'Assignment failed']), 500);
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
