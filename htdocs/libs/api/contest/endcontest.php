<?php

${basename(__FILE__, '.php')} = function () {

    $params = ['contestId'];

    if ($this->paramsExists($params)) {

        $contestId = $this->_request['contestId'];

        $contest = new Contest($contestId);

        $result = $contest->endnow();

        if ($result) {
            $this->response($this->json([
                'message' => 'Contest ended successfully!',
                "contestId" => (string) $contestId
            ]), 200);
        } else {
            $this->response($this->json(['message' => 'Failed to start contest']), 500);
        }

    } else {
        $this->response($this->json(['message' => 'Bad request']), 400);
    }
};
