<?php


${basename(__FILE__, '.php')} = function () {

    $params = ['contestId', 'studentId', 'facultyId'];

    if ($this->paramsExists($params)) {

        if (!$this->isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $contestId = $this->_request['contestId'];
        $studentId = $this->_request['studentId'];
        $facultyId = $this->_request['facultyId'];


        try {

            $result = ContestRegistration::confirmRegistration($contestId, $studentId, $facultyId);

            if ($result) {
                $this->response($this->json(['message' => 'Registration confirmed successfully!']), 200);
            } else {
                $this->response($this->json(['message' => 'Registration not confirmed']), 500);
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/\b(?:not|no)\b/i', $errorMessage)) {
                $this->response($this->json(['message' => $errorMessage]), 404);
            } else {
                $this->response($this->json(['message' => $errorMessage]), 500);
            }
        }
    } else {
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};
