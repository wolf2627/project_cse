<?php


${basename(__FILE__, '.php')} = function () {

    $requiredParams = ['contestId', 'participantId'];

    if ($this->paramsExists($requiredParams)) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
            return;
        }

        $contestId = $this->_request['contestId'];
        $participantId = $this->_request['participantId'];

        try {
            $participant = new ContestParticipants($participantId, $contestId);
            $questions = $participant->endContest();
            $this->response($this->json([
                'message' => 'success',
            ]), 200);
        } catch (Exception $e) {
            $this->response($this->json(['message' => $e->getMessage()]), 400);
        }
    } else {
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};