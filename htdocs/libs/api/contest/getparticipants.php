<?php

${basename(__FILE__, '.php')} = function () {

    $params = ['contestId'];

    if ($this->paramsExists($params)) {

        if (!$this->isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $contestId = $this->_request['contestId'];

        try {
            $contest = new Contest($contestId);

            $result = $contest->getParticipants();

            if (!empty($result)) {
                $this->response($this->json([
                    'message' => 'success',
                    'registrations' => $result,
                ]), 200);
            } else {
                $this->response($this->json(['message' => 'No students registered']), 404);
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
