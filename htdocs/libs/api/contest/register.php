<?php

${basename(__FILE__, '.php')} = function () {

    $params = ['contestId'];

    if ($this->paramsExists($params)) {


        if (!$this->isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $contestId = $this->_request['contestId'];

        // TODO: Check if the user is a student or teacher. implement the check here
        if(Session::getUser()->getRole() !== 'student' && Session::getUser()->getRole() !== 'admin') {
            $this->response($this->json(['message' => 'Unauthorized']), 403);
            return;
        }

        $studentId = $this->_request['studentId'] ?? Session::getUser()->getRegNo();

        try {

            //throw new Exception('Not implemented');
            $result = ContestRegistration::registerForContest($contestId, $studentId);

            if ($result) {
                $this->response($this->json([
                    'message' => 'Registered successfully!',
                    "registrationId" => (string) $result
                ]), 200);
            } else {
                $this->response($this->json(['message' => 'Registration failed']), 500);
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
