<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['subject_code'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $subject_code = $this->_request['subject_code'];

        $result = Classes::getBatch($subject_code);

        if (!$result) {
            $this->response(
                $this->json([
                    'success' => false,
                    'message' => 'Record not found.'
                ]),
                404 // Internal Server Error
            );
        }

        $this->response(
            $this->json([
                'success' => true,
                'batches' => $result
            ]),
            200
        );
    } else {
        // Bad request response Missing Parameters
        $this->response(
            $this->json([
                'success' => false,
                'message' => 'Bad request'
            ]),
            400 // Bad Request
        );
    }
};
