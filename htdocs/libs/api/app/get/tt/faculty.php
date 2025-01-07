<?php

${basename(__FILE__, '.php')} = function () {

    if ($this->paramsExists(['batch', 'subject_code'])) {

        if (!Session::isAuthenticated()) {
            $this->response($this->json(['message' => 'Unauthorized']), 401);
        }

        $batch = $this->_request['batch'];
        $subject_code = $this->_request['subject_code'];

        error_log('Batch: ' . $batch . ' Subject Code: ' . $subject_code);

        $result = Classes::getFaculties($subject_code, $batch);

        if (!$result) {
            $this->response(
                $this->json([
                    'success' => false,
                    'message' => 'No faculties found.'
                ]),
                404 // Internal Server Error
            );
        }

        $this->response(
            $this->json([
                'success' => true,
                'faculties' => $result
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