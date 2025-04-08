<?php


${basename(__FILE__, '.php')} = function () {

    $requiredParams = ['contestId', 'roundId', 'participantId', 'questionId', 'submissionType', 'code', 'language'];

    if ($this->paramsExists($requiredParams)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $contestId = $this->_request['contestId'];
        $roundId = $this->_request['roundId'];
        $participantId = $this->_request['participantId'];
        $questionId = $this->_request['questionId'];
        $submissionType = $this->_request['submissionType'];
        $code = $this->_request['code'];
        $language = $this->_request['language'];

        try {
            $result = ContestSubmissions::submitCodingAnswer($contestId, $roundId, $questionId, $participantId, $submissionType, $code, $language);

            $this->response($this->json([
                'message' => 'Submission successful',
                'result' => $result
            ]), 200);
        } catch (Exception $e) {
            $this->response($this->json(['message' => $e->getMessage()]), 400);
            return;
        }
    } else {
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};
