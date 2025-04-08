<?php

${basename(__FILE__, '.php')} = function () {

    $requiredParams = ['faculty_id'];

    if ($this->paramsExists($requiredParams)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $faculty_id = $this->_request['faculty_id'];

        try {
            $result = Tutor::unassignTutor($faculty_id);
            if ($result) {
                $this->response($this->json([
                    'success' => true,
                    'message' => 'Tutor unassigned'
                ]), 200);
            } else {
                $this->response($this->json([
                    'success' => false,
                    'message' => 'Removal failed'
                ]), 500);
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
