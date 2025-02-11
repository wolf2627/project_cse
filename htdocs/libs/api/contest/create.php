<?php

// This API returns the list of students assigned to a faculty for a particular subject.

${basename(__FILE__, '.php')} = function () {

    $params = ['title', 'description', 'contestType', 'totalRounds', 'startTime', 'endTime', 'registrationDeadline', 'facultyId', 'coordinators'];

    if ($this->paramsExists($params)) {


        if (!$this->isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $title = $this->_request['title'];
        $description = $this->_request['description'];
        $contestType = $this->_request['contestType'];
        $totalRounds = $this->_request['totalRounds'];
        $startTime = $this->_request['startTime'];
        $endTime = $this->_request['endTime'];
        $registrationDeadline = $this->_request['registrationDeadline'];
        $facultyId = $this->_request['facultyId'];
        $coordinators = $this->_request['coordinators'];

        try {
            $result = Contest::createContest($title, $description, $contestType, $totalRounds, $startTime, $endTime, $registrationDeadline, $facultyId, $coordinators);

            if ($result) {
                $this->response($this->json(['message' => 'Contest created successfully!']), 200);
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
