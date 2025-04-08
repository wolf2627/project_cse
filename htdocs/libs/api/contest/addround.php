<?php

${basename(__FILE__, '.php')} = function () {

    $params = ['contestId', 'name', 'roundNumber', 'startTime', 'endTime'];

    if ($this->paramsExists($params)) {

        if (!$this->isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $contestId = $this->_request['contestId'];
        $name = $this->_request['name'];
        $roundNumber = $this->_request['roundNumber'];
        $startTime = $this->_request['startTime'];
        $endTime = $this->_request['endTime'];

        try {
            $result = Contest::contestRound($contestId, $name, $roundNumber, $startTime, $endTime);

            if ($result) {
                $this->response($this->json(['message' => 'Round created successfully!']), 200);
            } else {
                $this->response($this->json(['message' => 'Round not created']), 500);
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
