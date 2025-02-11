<?php

// This API returns the list of students assigned to a faculty for a particular subject.

${basename(__FILE__, '.php')} = function () {

    $requiredParams = ['type'];

    if ($this->paramsExists($requiredParams)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $type = $this->_request['type'];

        if($type == 'coding') {
            $question = $this->_request['question'];
            $sampletestcase = $this->_request['sampletestcase'];

            $Question = new ContestQuestions();
            $Question->addCodingQuestion($type, $Question, $sampletestcase);

            $this->response($this->json(['message' => 'Coding question added successfully!']), 200);
        } else if($type == 'mcq') {
            $question = $this->_request['question'];
            $options = $this->_request['options'];
            $correctOption = $this->_request['correctOption'];

            $Question = new ContestQuestions();
            $difficulty = $this->_request['difficulty'];
            $category = $this->_request['category'];
            $Question->addMCQQuestion($question, $options, $correctOption, $difficulty, $category);

            $this->response($this->json(['message' => 'MCQ question added successfully!']), 200);
        } else {
            $this->response($this->json(['message' => 'Invalid question type']), 400);
        }


    } else {
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};
