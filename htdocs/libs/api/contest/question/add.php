<?php


${basename(__FILE__, '.php')} = function () {

    $requiredParams = ['type'];

    if ($this->paramsExists($requiredParams)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $type = $this->_request['type'];

        if($type == 'coding') {
            
            $title = $this->_request['title'];
            $description = $this->_request['description'];
            $test_cases = $this->_request['testcases'];
            $difficulty = $this->_request['difficulty'];
            $contestId = $this->_request['contestId'];
            $roundId = $this->_request['roundId'];

            $Question = new ContestQuestions();
            $Question->addCodingQuestion($type, $title, $description, $test_cases, $difficulty, $contestId, $roundId);

            $this->response($this->json(['message' => 'Coding question added successfully!']), 200);
        } else if($type == 'mcq') {
            // TODO: Add MCQ question functionality
            $this->response($this->json(['message' => 'not implemented']), 200);
        } else {
            $this->response($this->json(['message' => 'Invalid question type']), 400);
        }


    } else {
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};
