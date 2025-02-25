<?php


${basename(__FILE__, '.php')} = function () {

    $params = ['title', 'description', 'contestType', 'startTime', 'endTime', 'registrationDeadline', 'facultyId'];

    if ($this->paramsExists($params)) {


        if (!$this->isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $title = $this->_request['title'];
        $description = $this->_request['description'];
        $contestType = $this->_request['contestType'];
        $startTime = $this->_request['startTime'];
        $endTime = $this->_request['endTime'];
        $registrationDeadline = $this->_request['registrationDeadline'];
        $facultyId = $this->_request['facultyId'];

        try {
            $result = Contest::createContest($title, $description, $contestType, $startTime, $endTime, $registrationDeadline, $facultyId);

            if ($result) {
                $this->response($this->json([
                    'message' => 'Contest created successfully!',
                    'contestId' => (string) $result
                ]), 200);
            } else {
                $this->response($this->json(['message' => 'Contest not created']), 500);
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
