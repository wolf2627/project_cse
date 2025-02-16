<?php

${basename(__FILE__, '.php')} = function () {

    $params = ['contestId', 'studentId'];

    if ($this->paramsExists($params)) {

        if (!$this->isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $contestId = $this->_request['contestId'];
        $studentId = $this->_request['studentId'];

        if (Session::getUser()->getRole() === 'faculty') {
            $approverId = Session::getUser()->getFacultyId();
        } else if (Session::getUser()->getRole() === 'admin') {
            $approverId = Session::getUser()->getAdminId();
        } else if (Session::getUser()->getRole() === 'student') {
            $approverId = Session::getUser()->getRegNo();
        }


        try {

            $result = ContestRegistration::confirmRegistration($contestId, $studentId, $approverId);

            if ($result) {
                $this->response($this->json(['message' => 'success']), 200);
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
